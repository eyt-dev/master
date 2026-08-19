<?php

namespace App\Http\Controllers\Api\Add2Farm;

use App\Http\Controllers\Controller;
use App\Services\Add2Farm\TranslationService;

class BaseController extends Controller
{
    protected TranslationService $translationService;

    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }

    /**
     * Get type label based on type value
     */
    protected function getTypeLabel(int $type): string
    {
        return $this->translationService->getTypeLabel($type);
    }

    /**
     * Get status label based on status value
     */
    protected function getStatusLabel(string $status): string
    {
        return $this->translationService->getStatusLabel($status);
    }
}
