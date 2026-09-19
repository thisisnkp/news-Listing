<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TableRow extends Model
{
    protected $fillable = [
        'dynamic_table_id',
        'plan_id',
        'package_id',
        'data',
        'order',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function dynamicTable(): BelongsTo
    {
        return $this->belongsTo(DynamicTable::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(RowTranslation::class);
    }

    /**
     * Get translation for a specific language
     */
    public function getTranslation(string $languageCode): ?RowTranslation
    {
        return $this->translations()
            ->whereHas('language', function ($q) use ($languageCode) {
                $q->where('code', $languageCode);
            })
            ->first();
    }

    /**
     * Get translated data for specific language with fallback
     */
    public function getTranslatedData(string $languageCode = null): array
    {
        $data = $this->data ?? [];

        if (!$languageCode) {
            $defaultLang = Language::getDefault();
            $languageCode = $defaultLang ? $defaultLang->code : 'en';
        }

        $translation = $this->getTranslation($languageCode);

        if ($translation) {
            $translatedData = $translation->translated_data ?? [];
            $data = array_merge($data, $translatedData);
        } else {
            // Fallback to default language
            $defaultLang = Language::getDefault();
            if ($defaultLang && $defaultLang->code !== $languageCode) {
                $fallbackTranslation = $this->getTranslation($defaultLang->code);
                if ($fallbackTranslation) {
                    $data = array_merge($data, $fallbackTranslation->translated_data ?? []);
                }
            }
        }

        return $data;
    }

    /**
     * Set translation for a language
     */
    public function setTranslation(int $languageId, array $translatedData): RowTranslation
    {
        return $this->translations()->updateOrCreate(
            ['language_id' => $languageId],
            ['translated_data' => $translatedData]
        );
    }

    /**
     * Get column value by slug
     */
    public function getValue(string $columnSlug, string $languageCode = null)
    {
        $data = $this->getTranslatedData($languageCode);
        return $data[$columnSlug] ?? null;
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
