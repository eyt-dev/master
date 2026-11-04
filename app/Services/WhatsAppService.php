<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send an approved WhatsApp template message via the Cloud API
     *
     * @param string $to Recipient number in international format (e.g. +919876543210)
     * @param string $templateName Approved template name (e.g. hello_world)
     * @param string $languageCode Template language code (e.g. en_US)
     * @return array{success: bool, message: string, message_id?: string, error?: array}
     */
    public function sendTemplate(string $to, string $templateName, string $languageCode = 'en_US'): array
    {
        $accessToken = config('services.whatsapp.access_token');
        $phoneNumberId = config('services.whatsapp.phone_number_id');

        if (!$accessToken || !$phoneNumberId) {
            return [
                'success' => false,
                'message' => 'WhatsApp credentials are not configured.',
            ];
        }

        $url = sprintf(
            'https://graph.facebook.com/%s/%s/messages',
            config('services.whatsapp.api_version'),
            $phoneNumberId
        );

        try {
            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->timeout(15)
                ->post($url, [
                    'messaging_product' => 'whatsapp',
                    'to' => preg_replace('/\D/', '', $to),
                    'type' => 'template',
                    'template' => [
                        'name' => $templateName,
                        'language' => ['code' => $languageCode],
                    ],
                ]);
        } catch (ConnectionException $e) {
            Log::error('WhatsApp connection error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Could not connect to the WhatsApp Cloud API.',
            ];
        }

        if ($response->failed()) {
            $error = $response->json('error') ?? [];

            Log::error('WhatsApp API error', ['status' => $response->status(), 'error' => $error]);

            return [
                'success' => false,
                'message' => $error['message'] ?? 'WhatsApp API request failed.',
                'error' => $error,
            ];
        }

        return [
            'success' => true,
            'message' => 'WhatsApp message sent successfully.',
            'message_id' => $response->json('messages.0.id'),
        ];
    }
}
