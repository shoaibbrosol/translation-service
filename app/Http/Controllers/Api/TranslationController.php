<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TranslationCollection;
use App\Http\Resources\TranslationResource;
use App\Models\Translation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class TranslationController extends Controller
{
    /**
     * Display a listing of translations.
     */
    public function index(Request $request): TranslationCollection
    {
        $translations = Translation::with('tags')
            ->filter($request->all())
            ->paginate($request->per_page ?? 20);

        return new TranslationCollection($translations);
    }

    /**
     * Store a newly created translation.
     *
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $this->validateRequest($request);

        $translation = Translation::create($validated);

        $this->syncTags($translation, $validated['tags'] ?? []);

        return response()->json(
            new TranslationResource($translation->load('tags')),
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified translation.
     */
    public function show($id): JsonResponse
    {
        $translation = Translation::with('tags')->findOrFail($id);
        return response()->json(
            new TranslationResource($translation)
        );
    }

    /**
     * Update the specified translation.
     *
     * @throws ValidationException
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validated = $this->validateRequest($request);
        $translation = Translation::findOrFail($id);
        $translation->update($validated);

        $this->syncTags($translation, $validated['tags'] ?? []);

        return response()->json(
            new TranslationResource($translation->load('tags'))
        );
    }

    /**
     * Remove the specified translation.
     */
    public function destroy($id): JsonResponse
    {
        $translation = Translation::findOrFail($id);
        $translation->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Validate the request data.
     *
     * @throws ValidationException
     */
    protected function validateRequest(Request $request): array
    {
        return $request->validate([
            'key' => 'required|string|max:255',
            'value' => 'required|string',
            'locale' => 'required|string|max:10',
            'tags' => 'sometimes|array',
            'tags.*' => 'string|max:50',
        ]);
    }

    /**
     * Sync translation tags.
     */
    protected function syncTags(Translation $translation, array $tags): void
    {
        $translation->tags()->delete();

        if (!empty($tags)) {
            $translation->tags()->createMany(
                array_map(
                    fn (string $tag) => ['tag' => $tag],
                    $tags
                )
            );
        }
    }
}
