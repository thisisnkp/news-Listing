<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Admin-managed visuals for the section blocks on /studio and /pr-services.
        Schema::create('page_images', function (Blueprint $table) {
            $table->id();
            $table->string('page_slug', 32);            // studio | pr-services
            $table->string('section_key', 48);          // gallery | network | founder_photo | ...
            $table->string('image')->nullable();        // uploaded file — Laravel storage path
            $table->string('legacy_path', 512)->nullable(); // pre-existing file under /assets, or an absolute URL
            $table->string('video_url', 512)->nullable();   // embed URL, for video slots only
            $table->string('alt', 512)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['page_slug', 'section_key', 'sort_order']);
        });

        $this->seedFromCurrentPages();
    }

    public function down(): void
    {
        Schema::dropIfExists('page_images');
    }

    /**
     * Seed every slot with exactly what studio.php / pr-services.php hardcode today,
     * so switching the pages over to the DB changes nothing visually on day one.
     */
    private function seedFromCurrentPages(): void
    {
        $now  = now();
        $rows = [
            [
                'page_slug'   => 'studio',
                'section_key' => 'package_video',
                'video_url'   => 'https://www.youtube.com/embed/gtyv1QnOBTk',
                'alt'         => 'RV Rising Studio Tour',
            ],
            [
                'page_slug'   => 'pr-services',
                'section_key' => 'network',
                'legacy_path' => 'assets/imgs/pr/network.webp',
                'alt'         => 'Media partners of RV Rising Media — Times of India, Forbes, PTI, Zee News, The Hindu, Hindustan Times, Mid-Day, India Today, Business Standard, NDTV, Republic, News18, Aaj Tak, ABP and 30+ more',
            ],
            [
                'page_slug'   => 'pr-services',
                'section_key' => 'clients',
                'legacy_path' => 'assets/imgs/pr/clients.webp',
                'alt'         => 'Clients of RV Rising Media — Flipkart, FIITJEE, Physics Wallah, HDFC ERGO, HSBC, Myntra, Rapido, Hettich, Vibgyor High, and 3,000+ more',
            ],
            [
                'page_slug'   => 'pr-services',
                'section_key' => 'why_us',
                'legacy_path' => 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=1000&q=80',
                'alt'         => 'Press conference and media coverage',
            ],
            [
                'page_slug'   => 'pr-services',
                'section_key' => 'founder_photo',
                'legacy_path' => 'assets/imgs/pr/founder.jpeg',
                'alt'         => 'Rahul Varun — Founder, RV Rising Media',
            ],
            [
                'page_slug'   => 'pr-services',
                'section_key' => 'founder_celeb',
                'legacy_path' => 'assets/imgs/pr/Firefly.webp',
                'alt'         => 'Celebrities and brands featured by RV Rising Media',
            ],
        ];

        foreach ($this->currentGalleryFiles() as $i => $file) {
            $rows[] = [
                'page_slug'   => 'studio',
                'section_key' => 'gallery',
                'legacy_path' => 'assets/imgs/studio/' . $file,
                'alt'         => 'RV Rising Studio Photo ' . ($i + 1),
                'sort_order'  => $i,
            ];
        }

        DB::table('page_images')->insert(array_map(fn ($r) => $r + [
            'image'      => null,
            'legacy_path'=> null,
            'video_url'  => null,
            'alt'        => null,
            'sort_order' => 0,
            'is_active'  => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ], $rows));
    }

    /**
     * The studio gallery is currently built by globbing assets/imgs/studio at render time:
     * WhatsApp photos newest-first, then the numbered 1–9 set. Mirror that order here so the
     * admin list opens showing the live gallery. Returns [] if the folder isn't reachable,
     * in which case studio.php keeps using its own glob until images are added.
     */
    private function currentGalleryFiles(): array
    {
        $dir = base_path('../assets/imgs/studio');
        if (!is_dir($dir)) {
            return [];
        }

        $files = glob($dir . '/WhatsApp Image*.jpeg') ?: [];
        usort($files, fn ($a, $b) => filemtime($b) <=> filemtime($a));
        $names = array_map('basename', $files);

        for ($i = 1; $i <= 9; $i++) {
            if (is_file($dir . '/' . $i . '.jpeg')) {
                $names[] = $i . '.jpeg';
            }
        }

        return $names;
    }
};
