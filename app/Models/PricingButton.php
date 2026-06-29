<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingButton extends Model
{
    protected $fillable = [
        'label',
        'icon',
        'url',
        'new_tab',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'new_tab'    => 'boolean',
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeOrdered($q)
    {
        return $q->orderBy('sort_order')->orderBy('id');
    }
}
