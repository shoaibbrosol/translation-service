<?php

namespace Tests\Feature;

use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_performance_with_large_dataset()
    {
        // Disable middleware for performance testing
        $this->withoutMiddleware();

        // Generate translations in optimized batches
        $batchSize = 5000;
        $totalRecords = 100000;

        for ($i = 0; $i < $totalRecords / $batchSize; $i++) {
            $translations = [];

            for ($j = 0; $j < $batchSize; $j++) {
                $translations[] = [
                    'key' => 'key-' . ($i * $batchSize + $j),
                    'value' => 'Translation ' . ($i * $batchSize + $j),
                    'locale' => $i % 2 ? 'en' : 'fr',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            Translation::insert($translations);
        }

        $this->assertDatabaseCount('translations', $totalRecords);

        // Warm up
        $this->get('/api/translations/export');

        // Measure
        $start = microtime(true);
        $response = $this->get('/api/translations/export');
        $duration = microtime(true) - $start;

        // Always output duration
        fwrite(STDERR, "\nExport duration: " . round($duration*1000, 2) . "ms\n");

        // Assertions
        $response->assertStatus(200);
        $this->assertLessThan(0.5, $duration); // 500ms in seconds
    }
}
