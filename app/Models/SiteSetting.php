<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
    ];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value, string $type = 'text'): self
    {
        Cache::forget("setting_{$key}");

        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type]
        );
    }

    /**
     * Get logo URL
     */
    public static function getLogo(): ?string
    {
        $logo = static::get('site_logo');
        return $logo ? Storage::url($logo) : null;
    }

    /**
     * Get favicon URL
     */
    public static function getFavicon(): ?string
    {
        $favicon = static::get('site_favicon');
        return $favicon ? Storage::url($favicon) : null;
    }

    /**
     * Get all settings as array
     */
    public static function getAllSettings(): array
    {
        return Cache::remember('all_settings', 3600, function () {
            return static::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache(): void
    {
        $settings = static::all();
        foreach ($settings as $setting) {
            Cache::forget("setting_{$setting->key}");
        }
        Cache::forget('all_settings');
    }
}
