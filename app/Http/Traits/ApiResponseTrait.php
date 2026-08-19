<?php

namespace App\Http\Traits;

use App\Services\Add2Farm\TranslationService;

/**
 * Trait for standardized API responses with multi-language support
 *
 * Usage in any controller:
 *   - $this->successResponse('User', 'created_successfully', $user)
 *   - $this->errorResponse('unauthorized_action', 401)
 *   - $this->message('auth.login_successful')
 */
trait ApiResponseTrait
{
    protected ?TranslationService $translationService = null;

    /**
     * Get translation service instance
     */
    protected function getTranslationService(): TranslationService
    {
        if (!$this->translationService) {
            $this->translationService = app(TranslationService::class);
        }
        return $this->translationService;
    }

    /**
     * Get translated message
     *
     * @param string $key Message key (e.g., 'auth.login_successful', 'crud.created_successfully')
     * @param array $replace Replacements for placeholders (e.g., [':resource' => 'User'])
     * @param string|null $locale Optional locale override
     * @return string
     */
    protected function message(string $key, array $replace = [], ?string $locale = null): string
    {
        // Split key by dot for nested translation files
        $parts = explode('.', $key);

        if (count($parts) === 1) {
            // Fallback to add2farm messages for backward compatibility
            return $this->getTranslationService()->get($key, $locale);
        }

        // Use messages.php file for generic messages
        return trans("messages.{$key}", $replace, $locale ?? app()->getLocale());
    }

    /**
     * Success response with translated message
     *
     * @param string $resourceName Name of the resource (e.g., 'User', 'Farm')
     * @param string $action Action performed (e.g., 'created_successfully', 'updated_successfully')
     * @param mixed $data Data to return
     * @param int $statusCode HTTP status code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse(
        string $resourceName,
        string $action,
        mixed $data = null,
        int $statusCode = 200
    ): \Illuminate\Http\JsonResponse
    {
        $messageKey = "messages.crud.{$action}";
        $message = trans($messageKey, [':resource' => trans("messages.resources.{$resourceName}")]);

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Error response with translated message
     *
     * @param string $messageKey Message key (e.g., 'auth.unauthorized', 'validation.operation_failed')
     * @param int $statusCode HTTP status code
     * @param array $replace Replacements for placeholders
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse(
        string $messageKey,
        int $statusCode = 400,
        array $replace = []
    ): \Illuminate\Http\JsonResponse
    {
        $message = trans("messages.{$messageKey}", $replace);

        return response()->json([
            'success' => false,
            'message' => $message,
        ], $statusCode);
    }

    /**
     * Error response with validation errors
     *
     * @param array $errors Validation errors from validator
     * @return \Illuminate\Http\JsonResponse
     */
    protected function validationErrorResponse(array $errors): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => false,
            'errors' => $errors,
        ], 422);
    }

    /**
     * Unauthorized response
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function unauthorizedResponse(): \Illuminate\Http\JsonResponse
    {
        return $this->errorResponse('auth.unauthorized', 401);
    }

    /**
     * Not found response
     *
     * @param string $resourceName Name of the resource
     * @return \Illuminate\Http\JsonResponse
     */
    protected function notFoundResponse(string $resourceName): \Illuminate\Http\JsonResponse
    {
        return $this->errorResponse('crud.not_found', 404, [':resource' => trans("messages.resources.{$resourceName}")]);
    }
}
