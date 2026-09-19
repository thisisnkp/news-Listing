<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class DynamicTable extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'services',
        'description',
        'price',
        'order_button_link',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'services' => 'array',
        'price' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            if (empty($table->slug)) {
                $table->slug = Str::slug($table->name);
            }
        });
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
     * Get transltable columns for this table
     */
    public function translatableColumns()
    {
        return $this->columns()->where('is_translatable', true)->get();
    }

    /**
     * Get filterable columns for this table
     */
    public function filterableColumns()
    {
        return $this->columns()->where('is_filterable', true)->get();
    }
}
