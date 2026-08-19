<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\Add2Farm\TranslationService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Global middleware to set language for all API routes
 *
 * Detects language from (in priority order):
 * 1. Query parameter: ?language=ar
 * 2. Accept-Language header: Accept-Language: ar
 * 3. Language header: Language: ar
 * 4. Default: English (en)
 */
class SetApiLanguage
{
    protected TranslationService $translationService;

    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        // Detect language from request
        $language = $request->query('language')
            ?? $request->header('Accept-Language')
            ?? $request->header('language')
            ?? config('app.locale', 'en');

        // Normalize and set the language
        $normalizedLanguage = $this->translationService->normalizeLocale($language);
        $this->translationService->setLocale($normalizedLanguage);

        // Store in request for easy access
        $request->merge(['_language' => $normalizedLanguage]);

        return $next($request);
    }
}
