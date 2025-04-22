<?php

namespace Database\Factories;

use App\Models\Translation;
use Illuminate\Database\Eloquent\Factories\Factory;

class TranslationFactory extends Factory
{
    protected $model = Translation::class;

    public function definition()
    {
        return [
            'key' => $this->faker->word . '-' . uniqid(), // Add unique identifier
            'value' => $this->faker->sentence,
            'locale' => $this->faker->randomElement(['en', 'fr', 'es', 'de']),
        ];
    }
}
