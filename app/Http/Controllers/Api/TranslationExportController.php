<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Translation;
use Illuminate\Support\Facades\Cache;

class TranslationExportController extends Controller
{
    public function export()
    {
        return Cache::remember('translations.export', now()->addHour(), function () {
            return Translation::with('tags')
                ->get()
                ->groupBy('locale')
                ->map(function ($translations) {
                    return $translations->mapWithKeys(function ($translation) {
                        return [
                            $translation->key => [
                                'value' => $translation->value,
                                'tags' => $translation->tags->pluck('tag')
                            ]
                        ];
                    });
                });
        });
    }
}
