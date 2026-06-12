<?php

namespace Database\Seeders;

use App\Models\PageSeo;
use Illuminate\Database\Seeder;

/**
 * Populates the seeded page_seos rows with production-ready SEO defaults
 * for https://rvrising.com/. Re-runs safely — uses page_slug as the key
 * and updates without touching og_image / custom_head (admin-managed).
 *
 * Run:
 *   php artisan db:seed --class=PageSeoDefaultsSeeder
 */
class PageSeoDefaultsSeeder extends Seeder
{
    public function run(): void
    {
        $base   = 'https://rvrising.com';
        $robots = 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';

        $pages = [
            [
                'page_slug'          => 'home',
                'page_label'         => 'Home',
                'meta_title'         => 'RV Rising Media — Mumbai PR Agency & Production House Since 2017',
                'meta_description'   => "India's trusted PR agency in Mumbai. Public relations, digital PR, celebrity management, press release distribution & brand building. 800+ brands trust us.",
                'meta_keywords'      => 'PR Agency Mumbai, Best PR Agency India, Public Relations Mumbai, RV Rising Media, Press Release Distribution India, Celebrity Management Mumbai, Bollywood PR, Digital PR India, Mumbai Production House, Brand Building, Rahul Varun',
                'canonical_override' => $base . '/',
                'robots'             => $robots,
            ],

            [
                'page_slug'          => 'about',
                'page_label'         => 'About Us',
                'meta_title'         => 'About RV Rising Media — Mumbai PR Agency Founded by Rahul Varun',
                'meta_description'   => 'Learn about RV Rising Media — founded in 2017 by Bollywood journalist Rahul Varun (Rahul Mishra). 9+ years, 800+ clients, 500+ media tie-ups across India.',
                'meta_keywords'      => 'About RV Rising Media, Rahul Varun Founder, Rahul Mishra Journalist, Mumbai PR Agency History, Bollywood Journalist Mumbai, RV Rising Story, PR Agency Founder Mumbai, The Filmy Charcha, Attention India',
                'canonical_override' => $base . '/about',
                'robots'             => $robots,
            ],

            [
                'page_slug'          => 'services',
                'page_label'         => 'Our Services',
                'meta_title'         => 'PR & Media Services — Public Relations, Digital PR, Celebrity Management | RV Rising',
                'meta_description'   => 'Full-service PR offerings from RV Rising Media — public relations, digital PR, celebrity management, press conferences, ad films and influencer marketing in India.',
                'meta_keywords'      => 'PR Services Mumbai, Digital PR Services India, Celebrity Management Services, Press Release Distribution, Influencer Marketing Mumbai, Ad Film Production, Public Relations Services, Brand PR India, Press Conference Mumbai',
                'canonical_override' => $base . '/services',
                'robots'             => $robots,
            ],

            [
                'page_slug'          => 'pr-services',
                'page_label'         => 'PR Services',
                'meta_title'         => 'PR Services in India — Get Featured in 500+ Media | Starting ₹999',
                'meta_description'   => "Get your brand featured in India's top media — Times of India, ANI, Mid-Day, NewsX and 500+ outlets. Affordable PR packages from ₹999. 24-hour delivery.",
                'meta_keywords'      => 'PR Services India, Press Release India, Cheap PR Packages, PR Pricing India, Mumbai PR Pricing, ANI PR, PTI PR, Times of India PR, Tier 1 PR India, Affordable PR India, PR Packages ₹999, Digital PR India',
                'canonical_override' => $base . '/pr-services',
                'robots'             => $robots,
            ],

            [
                'page_slug'          => 'studio',
                'page_label'         => 'Studio',
                'meta_title'         => 'Podcast & Video Studio Mumbai — 4K Cinematic from ₹3,000/hr | RV Rising',
                'meta_description'   => 'Pro podcast and video studio in Andheri West, Mumbai. 3-camera 4K setup, pro mics, lights and crew included. Book from ₹3,000/hour. Used by Bollywood talent.',
                'meta_keywords'      => 'Podcast Studio Mumbai, Video Studio Andheri, Recording Studio Mumbai, Podcast Recording Mumbai, 4K Video Studio India, Bollywood Studio Mumbai, Studio Rental Mumbai, Podcast Studio Booking Mumbai, Oshiwara Studio',
                'canonical_override' => $base . '/studio',
                'robots'             => $robots,
            ],
        ];

        foreach ($pages as $row) {
            PageSeo::updateOrCreate(
                ['page_slug' => $row['page_slug']],
                $row
            );
        }
    }
}
