<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @group WhatsApp
 * Temporary APIs for testing the WhatsApp Cloud API integration
 */
class WhatsAppController extends Controller
{
    protected WhatsAppService $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Send a test Hello World WhatsApp message
     *
     * Sends the approved `hello_world` template to the given number.
     *
     * @authenticated
     * @bodyParam phone string required Recipient number in international format. Example: +919876543210
     *
     * @response 200 {
     *   "success": true,
     *   "message": "WhatsApp message sent successfully.",
     *   "message_id": "wamid.HBgLMTU1NTAxMjM0NTY3FQIAERgSMUQ1ODlENzVCQzBDNjI4NjgA"
     * }
     * @response 422 {
     *   "success": false,
     *   "errors": {
     *     "phone": ["The phone field format is invalid."]
     *   }
     * }
     * @response 502 {
     *   "success": false,
     *   "message": "Invalid OAuth access token.",
     *   "error": {"message": "Invalid OAuth access token.", "code": 190}
     * }
     */
    public function sendHelloWorld(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|regex:/^\+?[0-9]{8,15}$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->whatsAppService->sendTemplate($request->phone, 'hello_world');

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'message_id' => $result['message_id'] ?? null,
            'error' => $result['error'] ?? null,
        ], $result['success'] ? 200 : 502);
    }
}
