<?php

namespace App\Services\Add2Farm;

use Illuminate\Support\Facades\App;

class TranslationService
{
    /**
     * Supported languages for Add2Farm
     */
    protected array $supportedLanguages = ['en', 'ar'];

    /**
     * Default language
     */
    protected string $defaultLanguage = 'en';

    /**
     * Constructor
     */
    public function __construct()
    {
        // Use default locale if set, otherwise use 'en'
        $this->defaultLanguage = config('app.locale', 'en');
    }

    /**
     * Get a translated message
     *
     * @param string $key The translation key (e.g., 'otp_sent_successfully')
     * @param string|null $locale The locale to use (optional)
     * @param array $replace Replacements for placeholders
     * @return string
     */
    public function get(string $key, ?string $locale = null, array $replace = []): string
    {
        $locale = $this->normalizeLocale($locale);

        return trans("add2farm.messages.{$key}", $replace, $locale);
    }

    /**
     * Get a translated type label
     *
     * @param int $type The user type
     * @param string|null $locale The locale to use (optional)
     * @return string
     */
    public function getTypeLabel(int $type, ?string $locale = null): string
    {
        $locale = $this->normalizeLocale($locale);

        return trans("add2farm.user_types.{$type}", [], $locale) ?? "Unknown Type {$type}";
    }

    /**
     * Get a translated status label
     *
     * @param string $status The status value
     * @param string|null $locale The locale to use (optional)
     * @return string
     */
    public function getStatusLabel(string $status, ?string $locale = null): string
    {
        $key = strtolower($status);
        $locale = $this->normalizeLocale($locale);

        return trans("add2farm.status.{$key}", [], $locale) ?? $status;
    }

    /**
     * Get a translated field label
     *
     * @param string $field The field name
     * @param string|null $locale The locale to use (optional)
     * @return string
     */
    public function getFieldLabel(string $field, ?string $locale = null): string
    {
        $locale = $this->normalizeLocale($locale);

        return trans("add2farm.fields.{$field}", [], $locale) ?? $field;
    }

    /**
     * Normalize locale to a supported language
     *
     * @param string|null $locale
     * @return string
     */
    public function normalizeLocale(?string $locale): string
    {
        if (!$locale) {
            return App::getLocale() ?: $this->defaultLanguage;
        }

        $locale = explode('-', $locale)[0]; // Handle locales like 'en-US'
        $locale = strtolower($locale);

        return in_array($locale, $this->supportedLanguages) ? $locale : $this->defaultLanguage;
    }

    /**
     * Get supported languages
     *
     * @return array
     */
    public function getSupportedLanguages(): array
    {
        return $this->supportedLanguages;
    }

    /**
     * Set locale for current request
     *
     * @param string $locale
     * @return void
     */
    public function setLocale(string $locale): void
    {
        $locale = $this->normalizeLocale($locale);
        App::setLocale($locale);
    }
}
