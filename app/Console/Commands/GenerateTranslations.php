<?php

namespace App\Console\Commands;

use App\Models\Translation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateTranslations extends Command
{
    protected $signature = 'translations:generate {count=100000}';

    protected $description = 'Generate test translations';

    public function handle(): int
    {
        $count = (int) $this->argument('count');
        $locales = ['en', 'fr', 'es', 'de', 'it'];
        $tags = ['web', 'mobile', 'desktop', 'admin', 'public'];

        $this->info("Generating {$count} translations...");

        $bar = $this->output->createProgressBar($count);

        DB::transaction(function () use ($count, $locales, $tags, $bar) {
            Translation::factory()
                ->count($count)
                ->make()
                ->chunk(1000)
                ->each(function ($chunk) use ($locales, $tags, $bar) {
                    $translations = [];

                    foreach ($chunk as $translation) {
                        $translation->locale = $locales[array_rand($locales)];
                        $translations[] = $translation->toArray();
                    }

                    Translation::insert($translations);

                    $latestIds = Translation::latest()
                        ->take(count($chunk))
                        ->pluck('id');

                    $tagsToInsert = [];

                    foreach ($latestIds as $id) {
                        if (random_int(0, 1)) {
                            $tagsToInsert[] = [
                                'translation_id' => $id,
                                'tag' => $tags[array_rand($tags)],
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }

                    if (! empty($tagsToInsert)) {
                        DB::table('translation_tags')->insert($tagsToInsert);
                    }

                    $bar->advance(count($chunk));
                });
        });

        $bar->finish();
        $this->newLine();
        $this->info('Done!');

        return self::SUCCESS;
    }
}
