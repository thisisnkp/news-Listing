-- ============================================================================
-- RV Rising Media — production deployment SQL
-- Adds two new tables (testimonials, page_seos) + seeded SEO defaults
-- and registers both migrations so Laravel won't try to re-run them.
--
-- Safe to run multiple times: every statement is idempotent.
--
-- Usage (cPanel phpMyAdmin):
--   1) Open your live database
--   2) Click the "SQL" tab
--   3) Paste the entire contents of this file and run
--
-- Usage (mysql CLI):
--   mysql -h <host> -u <user> -p <database> < production-extra-tables.sql
-- ============================================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';

-- ----------------------------------------------------------------------------
-- 1. testimonials — homepage testimonials (text + video) managed via /pricing/admin
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `testimonials` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` ENUM('text','video') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `name` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` VARCHAR(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company` VARCHAR(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` TEXT COLLATE utf8mb4_unicode_ci,
  `image` VARCHAR(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_url` VARCHAR(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating` TINYINT UNSIGNED NOT NULL DEFAULT '5',
  `sort_order` INT UNSIGNED NOT NULL DEFAULT '0',
  `is_active` TINYINT(1) NOT NULL DEFAULT '1',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `testimonials_is_active_sort_order_index` (`is_active`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 2. page_seos — per-page SEO managed via /pricing/admin/page-seos
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `page_seos` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `page_slug` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_label` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_title` VARCHAR(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` TEXT COLLATE utf8mb4_unicode_ci,
  `meta_keywords` TEXT COLLATE utf8mb4_unicode_ci,
  `og_image` VARCHAR(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `canonical_override` VARCHAR(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `robots` VARCHAR(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `json_ld` TEXT COLLATE utf8mb4_unicode_ci,
  `custom_head` TEXT COLLATE utf8mb4_unicode_ci,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_seos_page_slug_unique` (`page_slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 3. Seed the 5 managed pages with production SEO defaults for rvrising.com
--    INSERT IGNORE = skip rows whose page_slug already exists (admin-managed).
-- ----------------------------------------------------------------------------
INSERT IGNORE INTO `page_seos`
  (`page_slug`, `page_label`, `meta_title`, `meta_description`, `meta_keywords`, `og_image`, `canonical_override`, `robots`, `json_ld`, `custom_head`, `created_at`, `updated_at`)
VALUES
(
  'home',
  'Home',
  'RV Rising Media — Mumbai PR Agency & Production House Since 2017',
  'India''s trusted PR agency in Mumbai. Public relations, digital PR, celebrity management, press release distribution & brand building. 800+ brands trust us.',
  'PR Agency Mumbai, Best PR Agency India, Public Relations Mumbai, RV Rising Media, Press Release Distribution India, Celebrity Management Mumbai, Bollywood PR, Digital PR India, Mumbai Production House, Brand Building, Rahul Varun',
  NULL,
  'https://rvrising.com/',
  'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1',
  NULL,
  NULL,
  NOW(),
  NOW()
),
(
  'about',
  'About Us',
  'About RV Rising Media — Mumbai PR Agency Founded by Rahul Varun',
  'Learn about RV Rising Media — founded in 2017 by Bollywood journalist Rahul Varun (Rahul Mishra). 9+ years, 800+ clients, 500+ media tie-ups across India.',
  'About RV Rising Media, Rahul Varun Founder, Rahul Mishra Journalist, Mumbai PR Agency History, Bollywood Journalist Mumbai, RV Rising Story, PR Agency Founder Mumbai, The Filmy Charcha, Attention India',
  NULL,
  'https://rvrising.com/about',
  'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1',
  NULL,
  NULL,
  NOW(),
  NOW()
),
(
  'services',
  'Our Services',
  'PR & Media Services — Public Relations, Digital PR, Celebrity Management | RV Rising',
  'Full-service PR offerings from RV Rising Media — public relations, digital PR, celebrity management, press conferences, ad films and influencer marketing in India.',
  'PR Services Mumbai, Digital PR Services India, Celebrity Management Services, Press Release Distribution, Influencer Marketing Mumbai, Ad Film Production, Public Relations Services, Brand PR India, Press Conference Mumbai',
  NULL,
  'https://rvrising.com/services',
  'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1',
  NULL,
  NULL,
  NOW(),
  NOW()
),
(
  'pr-services',
  'PR Services',
  'PR Services in India — Get Featured in 500+ Media | Starting ₹999',
  'Get your brand featured in India''s top media — Times of India, ANI, Mid-Day, NewsX and 500+ outlets. Affordable PR packages from ₹999. 24-hour delivery.',
  'PR Services India, Press Release India, Cheap PR Packages, PR Pricing India, Mumbai PR Pricing, ANI PR, PTI PR, Times of India PR, Tier 1 PR India, Affordable PR India, PR Packages ₹999, Digital PR India',
  NULL,
  'https://rvrising.com/pr-services',
  'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1',
  NULL,
  NULL,
  NOW(),
  NOW()
),
(
  'studio',
  'Studio',
  'Podcast & Video Studio Mumbai — 4K Cinematic from ₹3,000/hr | RV Rising',
  'Pro podcast and video studio in Andheri West, Mumbai. 3-camera 4K setup, pro mics, lights and crew included. Book from ₹3,000/hour. Used by Bollywood talent.',
  'Podcast Studio Mumbai, Video Studio Andheri, Recording Studio Mumbai, Podcast Recording Mumbai, 4K Video Studio India, Bollywood Studio Mumbai, Studio Rental Mumbai, Podcast Studio Booking Mumbai, Oshiwara Studio',
  NULL,
  'https://rvrising.com/studio',
  'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1',
  NULL,
  NULL,
  NOW(),
  NOW()
);

-- ----------------------------------------------------------------------------
-- 4. Register both migrations so `php artisan migrate` on production
--    sees them as already-applied and skips re-running them.
-- ----------------------------------------------------------------------------
INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
  ('2026_06_06_100001_create_testimonials_table', (SELECT IFNULL(MAX(b), 1) FROM (SELECT MAX(batch) AS b FROM migrations) AS m)),
  ('2026_06_06_100002_create_page_seos_table',    (SELECT IFNULL(MAX(b), 1) FROM (SELECT MAX(batch) AS b FROM migrations) AS m));

-- ============================================================================
-- DONE. Verify with:
--   SELECT page_slug, meta_title FROM page_seos;
--   SELECT COUNT(*) FROM testimonials;
-- ============================================================================
