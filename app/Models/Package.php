<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Package extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'visibility',
        'private_token',
        'remark',
        'order_button_link',
        'enabled_filters',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'enabled_filters' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($package) {
            if (empty($package->slug)) {
                $package->slug = Str::slug($package->name);
            }
            // Auto-generate private token for private packages
            if ($package->visibility === 'private' && empty($package->private_token)) {
                $package->private_token = Str::random(32);
            }
        });

        static::updating(function ($package) {
            // Generate token when switching to private
            if ($package->visibility === 'private' && empty($package->private_token)) {
                $package->private_token = Str::random(32);
            }
            // Clear token when switching to public
            if ($package->visibility === 'public') {
                $package->private_token = null;
            }
        });
    }

    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class)->orderBy('order');
    }

    public function activePlans(): HasMany
    {
        return $this->hasMany(Plan::class)->where('is_active', true)->orderBy('order');
    }

    /**
     * Get columns directly under this package (for media type)
     */
    public function columns(): HasMany
    {
        return $this->hasMany(TableColumn::class)->orderBy('order');
    }

    /**
     * Get rows directly under this package (for media type)
     */
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

    public function scopeMedia($query)
    {
        return $query->where('type', 'media');
    }

    public function scopePackage($query)
    {
        return $query->where('type', 'package');
    }

    /**
     * Check if this is a media type package
     */
    public function isMedia(): bool
    {
        return $this->type === 'media';
    }

    /**
     * Check if this is a package type (with plans)
     */
    public function isPackage(): bool
    {
        return $this->type === 'package';
    }

    /**
     * Scope to only include public packages
     */
    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    /**
     * Check if this package is private
     */
    public function isPrivate(): bool
    {
        return $this->visibility === 'private';
    }

    /**
     * Check if this package is public
     */
    public function isPublic(): bool
    {
        return $this->visibility === 'public';
    }

    /**
     * Get the private access URL for this package
     */
    public function getPrivateUrl(): ?string
    {
        if (!$this->isPrivate() || empty($this->private_token)) {
            return null;
        }
        return route('package.show', $this->slug) . '?token=' . $this->private_token;
    }
}
