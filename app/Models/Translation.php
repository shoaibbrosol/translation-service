<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Translation extends Model
{
    use HasFactory;
    protected $fillable = [
        'key',
        'value',
        'locale',
    ];

    public function tags(): HasMany
    {
        return $this->hasMany(TranslationTag::class);
    }

    public function scopeFilter($query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('key', 'like', '%' . $search . '%')
                    ->orWhere('value', 'like', '%' . $search . '%');
            });
        })->when($filters['locale'] ?? null, function ($query, $locale) {
            $query->where('locale', $locale);
        })->when($filters['tags'] ?? null, function ($query, $tags) {
            $query->whereHas('tags', function ($query) use ($tags) {
                $query->whereIn('tag', explode(',', $tags));
            });
        });
    }
}
