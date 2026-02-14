<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TableColumn extends Model
{
    protected $fillable = [
        'dynamic_table_id',
        'plan_id',
        'package_id',
        'name',
        'slug',
        'type',
        'is_translatable',
        'is_filterable',
        'is_sortable',
        'order',
        'name_if_button',
        'dropdown_options',
    ];

    protected $casts = [
        'is_translatable' => 'boolean',
        'is_filterable' => 'boolean',
        'is_sortable' => 'boolean',
        'dropdown_options' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($column) {
            if (empty($column->slug)) {
                $column->slug = Str::slug($column->name, '_');
            }
        });
    }

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

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function scopeTranslatable($query)
    {
        return $query->where('is_translatable', true);
    }

    public function scopeFilterable($query)
    {
        return $query->where('is_filterable', true);
    }

    public function scopeSortable($query)
    {
        return $query->where('is_sortable', true);
    }

    /**
     * Check if this column is numeric (number or currency)
     */
    public function isNumeric(): bool
    {
        return in_array($this->type, ['number', 'currency']);
    }

    /**
     * Check if this column is a dropdown
     */
    public function isDropdown(): bool
    {
        return $this->type === 'dropdown';
    }
}
