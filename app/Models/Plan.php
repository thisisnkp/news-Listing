<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Plan extends Model
{
    protected $fillable = [
        'package_id',
        'name',
        'slug',
        'services',
        'description',
        'price',
        'order_button_link',
        'enabled_filters',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'services' => 'array',
        'enabled_filters' => 'array',
        'price' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($plan) {
            if (empty($plan->slug)) {
                $plan->slug = Str::slug($plan->name);
            }
        });
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function columns(): HasMany
    {
        return $this->hasMany(TableColumn::class)->orderBy('order');
    }

    public function rows(): HasMany
    {
        return $this->hasMany(TableRow::class)->orderBy('order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Get translatable columns for this plan
     */
    public function translatableColumns()
    {
        return $this->columns()->where('is_translatable', true)->get();
    }

    /**
     * Get filterable columns for this plan
     */
    public function filterableColumns()
    {
        return $this->columns()->where('is_filterable', true)->get();
    }
}
