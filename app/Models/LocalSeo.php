<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LocalSeo extends Model
{
    protected $fillable = [
        'page_slug',
        'city',
        'city_slug',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'canonical_override',
        'robots',
        'json_ld',
        'custom_head',
        'hero_heading',
        'hero_subheading',
        'faqs',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /** The three base pages that support city landing pages. */
    public const PAGES = [
        'home'        => 'Home',
        'pr-services' => 'PR Services',
        'studio'      => 'Studio',
    ];

    /** URL path prefix for each base page. */
    public const PAGE_PREFIX = [
        'home'        => '/city',
        'pr-services' => '/pr-services',
        'studio'      => '/studio',
    ];

    public function pageLabel(): string
    {
        return self::PAGES[$this->page_slug] ?? $this->page_slug;
    }

    public function url(): string
    {
        return self::siteBaseUrl() . $this->publicPath();
    }

    /**
     * Root URL of the public PHP site that actually serves the city pages.
     *
     * The admin panel is mounted at "/pricing" (see pricing.php shim), so
     * config('app.url') sits one level below the site root — city pages live
     * at /city/{city}, not /pricing/city/{city}. Strip the mount prefix.
     */
    public static function siteBaseUrl(): string
    {
        $base = rtrim((string) config('app.url'), '/');

        return preg_replace('#/pricing$#', '', $base);
    }

    public function publicPath(): string
    {
        $prefix = self::PAGE_PREFIX[$this->page_slug] ?? '';
        return $prefix . '/' . $this->city_slug;
    }

    public function ogImageUrl(): ?string
    {
        return $this->og_image ? Storage::url($this->og_image) : null;
    }

    /** Decoded FAQ list: array of ['q' => ..., 'a' => ...]. */
    public function faqList(): array
    {
        if (empty($this->faqs)) return [];
        $decoded = json_decode($this->faqs, true);
        return is_array($decoded) ? $decoded : [];
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
}
