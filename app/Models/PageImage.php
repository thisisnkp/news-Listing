<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PageImage extends Model
{
    protected $fillable = [
        'page_slug',
        'section_key',
        'image',
        'legacy_path',
        'video_url',
        'alt',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /** Pages that expose editable section visuals, in tab order. Studio is the default tab. */
    public const PAGES = [
        'studio'      => 'Studio',
        'pr-services' => 'PR Services',
    ];

    /** Public path of each page, for the "view live" link. */
    public const PAGE_PATH = [
        'studio'      => '/studio',
        'pr-services' => '/pr-services',
    ];

    /**
     * The editable slots per page.
     *   type: 'single' → exactly one image   'multi' → an ordered gallery   'video' → an embed URL
     */
    public const SECTIONS = [
        'studio' => [
            'package_video' => [
                'label' => "What's In The Package",
                'type'  => 'video',
                'hint'  => 'The vertical video beside the checklist. Paste a YouTube embed URL (youtube.com/embed/ID).',
            ],
            'gallery' => [
                'label' => 'Studio Gallery',
                'type'  => 'multi',
                'hint'  => 'The "Inside The Studio" grid. First 9 show by default; the rest appear behind "View All Photos".',
            ],
        ],
        'pr-services' => [
            'network' => [
                'label' => 'Our Network',
                'type'  => 'single',
                'hint'  => 'The media-partners logo board.',
            ],
            'clients' => [
                'label' => 'Our Clients',
                'type'  => 'single',
                'hint'  => 'The client logo board.',
            ],
            'why_us' => [
                'label' => 'Why Choose Us',
                'type'  => 'single',
                'hint'  => 'The photo beside the feature list.',
            ],
            'founder_photo' => [
                'label' => 'About The Founder — Founder Photo',
                'type'  => 'single',
                'hint'  => 'Portrait in the founder block, with the name badge over it.',
            ],
            'founder_celeb' => [
                'label' => 'About The Founder — Celebrity Collage',
                'type'  => 'single',
                'hint'  => 'The "Stardom We\'ve Shaped" collage. Used twice — beside the portrait on desktop, inline on mobile.',
            ],
        ],
    ];

    public static function sectionsFor(string $pageSlug): array
    {
        return self::SECTIONS[$pageSlug] ?? [];
    }

    public function sectionMeta(): array
    {
        return self::SECTIONS[$this->page_slug][$this->section_key] ?? [
            'label' => $this->section_key,
            'type'  => 'single',
            'hint'  => '',
        ];
    }

    public function sectionLabel(): string
    {
        return $this->sectionMeta()['label'];
    }

    /**
     * Browser URL for this row's picture — an uploaded file wins over the seeded one.
     * legacy_path may be an absolute URL or a path relative to the main site root.
     */
    public function displayUrl(): ?string
    {
        if ($this->image) {
            return Storage::url($this->image);
        }

        if (!$this->legacy_path) {
            return null;
        }

        if (preg_match('#^https?://#i', $this->legacy_path)) {
            return $this->legacy_path;
        }

        $parts = array_map('rawurlencode', explode('/', ltrim($this->legacy_path, '/')));

        return LocalSeo::siteBaseUrl() . '/' . implode('/', $parts);
    }

    /** True once an admin has uploaded over the originally seeded file. */
    public function isUploaded(): bool
    {
        return (bool) $this->image;
    }

    /** Public URL of the page a slot belongs to, for the "view live" link. */
    public static function liveUrl(string $pageSlug): string
    {
        return LocalSeo::siteBaseUrl() . (self::PAGE_PATH[$pageSlug] ?? '/');
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
}
