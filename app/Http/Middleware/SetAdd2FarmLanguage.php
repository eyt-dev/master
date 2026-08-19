<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\Add2Farm\TranslationService;
use Symfony\Component\HttpFoundation\Response;

class SetAdd2FarmLanguage
{
    /**
     * Translation service instance
     */
    protected TranslationService $translationService;

    /**
     * Constructor
     */
    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Priority order for language detection:
        // 1. 'language' query parameter
        // 2. 'Accept-Language' header
        // 3. 'language' header
        // 4. Default language

        $language = $request->query('language')
            ?? $request->header('Accept-Language')
            ?? $request->header('language')
            ?? config('app.locale', 'en');

        // Normalize and set the language
        $this->translationService->setLocale($language);

        // Store in request for easy access
        $request->merge(['_language' => $this->translationService->normalizeLocale($language)]);

        return $next($request);
    }
}
