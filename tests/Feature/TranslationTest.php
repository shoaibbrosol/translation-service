<?php

namespace Tests\Feature;

use App\Models\Translation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }
    public function test_can_list_translations()
    {
        Translation::factory()->count(5)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->get('/api/translations?per_page=5');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data') // Check count in 'data' array
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'key',
                        'value',
                        'locale',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'links',
                'meta'
            ]);
    }

    /** @test */
    public function test_can_create_translation()
    {
        $data = [
            'key' => 'welcome.message',
            'value' => 'Welcome to our application',
            'locale' => 'en',
            'tags' => ['web', 'mobile']
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->post('/api/translations', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'key',
                'value',
                'locale',
                'tags',
                'created_at',
                'updated_at'
            ])
            ->assertJson([
                'key' => 'welcome.message',
                'locale' => 'en'
            ]);
    }

    /** @test */
    public function test_can_show_translation()
    {
        $translation = Translation::factory()->create([
            'key' => 'test.key',
            'value' => 'Test value',
            'locale' => 'en'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->get("/api/translations/{$translation->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $translation->id,
                'key' => 'test.key',
                'value' => 'Test value',
                'locale' => 'en'
            ]);
    }
    /** @test */
    public function can_update_translation()
    {
        $translation = Translation::factory()->create(['locale' => 'en']);
        $translation->tags()->create(['tag' => 'web']);

        $updateData = [
            'key' => 'updated.key',
            'value' => 'Updated value',
            'locale' => 'fr',
            'tags' => ['mobile', 'desktop']
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->put("/api/translations/{$translation->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonPath('data.key', 'updated.key')
            ->assertJsonPath('data.locale', 'fr')
            ->assertJsonPath('data.tags', ['mobile', 'desktop']);

        $this->assertDatabaseHas('translations', [
            'id' => $translation->id,
            'key' => 'updated.key',
            'locale' => 'fr'
        ]);
    }
    /** @test */
    public function can_delete_translation()
    {
        $translation = Translation::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->delete("/api/translations/{$translation->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('translations', ['id' => $translation->id]);
    }
}
