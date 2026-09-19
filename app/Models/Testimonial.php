<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Testimonial extends Model
{
    protected $fillable = [
        'type',
        'name',
        'role',
        'company',
        'city',
        'message',
        'image',
        'video_url',
        'rating',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rating'    => 'integer',
        'sort_order'=> 'integer',
    ];

    public function imageUrl(): ?string
    {
        return $this->image ? Storage::url($this->image) : null;
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
