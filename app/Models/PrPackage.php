<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrPackage extends Model
{
    protected $fillable = [
        'label',
        'name',
        'original_price',
        'price',
        'sub',
        'features',
        'badge',
        'is_popular',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_popular' => 'boolean',
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    /** Decoded bullet list. */
    public function featureList(): array
    {
        if (empty($this->features)) return [];
        $decoded = json_decode($this->features, true);
        return is_array($decoded) ? array_values(array_filter(array_map('trim', $decoded), fn ($f) => $f !== '')) : [];
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeOrdered($q)
    {
        return $q->orderBy('sort_order')->orderBy('id');
    }
}
