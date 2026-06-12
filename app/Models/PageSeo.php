<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PageSeo extends Model
{
    protected $fillable = [
        'page_slug',
        'page_label',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'canonical_override',
        'robots',
        'json_ld',
        'custom_head',
    ];

    public function ogImageUrl(): ?string
    {
        return $this->og_image ? Storage::url($this->og_image) : null;
    }

    public function getRouteKeyName(): string
    {
        return 'page_slug';
    }
}
