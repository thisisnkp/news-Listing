<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PageHeroImage extends Model
{
    protected $fillable = [
        'page_seo_id',
        'image',
        'alt',
        'sort_order',
    ];

    public function pageSeo(): BelongsTo
    {
        return $this->belongsTo(PageSeo::class);
    }

    public function imageUrl(): ?string
    {
        return $this->image ? Storage::url($this->image) : null;
    }
}
