<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RowTranslation extends Model
{
    protected $fillable = [
        'table_row_id',
        'language_id',
        'translated_data',
    ];

    protected $casts = [
        'translated_data' => 'array',
    ];

    public function tableRow(): BelongsTo
    {
        return $this->belongsTo(TableRow::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * Get value for a specific column
     */
    public function getValue(string $columnSlug)
    {
        return $this->translated_data[$columnSlug] ?? null;
    }

    /**
     * Set value for a specific column
     */
    public function setValue(string $columnSlug, $value): self
    {
        $data = $this->translated_data ?? [];
        $data[$columnSlug] = $value;
        $this->translated_data = $data;
        return $this;
    }
}
