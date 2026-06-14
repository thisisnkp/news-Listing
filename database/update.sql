-- ============================================================================
-- RV Rising Media — LIVE DATABASE UPDATE SCRIPT
-- ============================================================================
-- Run this ONCE on the production database to bring it fully up to date with
-- everything the admin panel (/pricing/admin) now needs:
--
--   • testimonials   — homepage / city testimonials (text + video) + city tag
--   • page_seos      — per-page SEO (5 default rows seeded)
--   • leads          — every contact/popup/studio form submission (dedupe + mail cooldown)
--   • local_seos     — city landing pages for home / pr-services / studio
--   • pr_packages    — PR pricing cards on /pr-services (6 default cards seeded)
--
-- 100% IDEMPOTENT — safe to run multiple times and safe whether the DB already
-- has some of these tables/columns or none of them. Nothing existing is
-- overwritten (CREATE TABLE IF NOT EXISTS / INSERT IGNORE / guarded ALTER).
--
-- HOW TO RUN
--   cPanel phpMyAdmin : open the live DB → "SQL" tab → paste this whole file → Go
--   mysql CLI         : mysql -h <host> -u <user> -p <database> < update.sql
-- ============================================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';

-- ----------------------------------------------------------------------------
-- 1. testimonials  (homepage + city testimonials)
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

-- Add the `city` tag column to testimonials if it isn't there yet (idempotent).
SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'testimonials' AND COLUMN_NAME = 'city'
);
SET @sql := IF(@col_exists = 0,
  'ALTER TABLE `testimonials` ADD COLUMN `city` VARCHAR(160) COLLATE utf8mb4_unicode_ci NULL AFTER `company`, ADD INDEX `testimonials_city_index` (`city`)',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ----------------------------------------------------------------------------
-- 2. page_seos  (per-page SEO managed via /pricing/admin/page-seos)
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

-- Seed the 5 managed pages with production SEO defaults (skipped if already present).
INSERT IGNORE INTO `page_seos`
  (`page_slug`, `page_label`, `meta_title`, `meta_description`, `meta_keywords`, `og_image`, `canonical_override`, `robots`, `json_ld`, `custom_head`, `created_at`, `updated_at`)
VALUES
(
  'home', 'Home',
  'RV Rising Media — Mumbai PR Agency & Production House Since 2017',
  'India''s trusted PR agency in Mumbai. Public relations, digital PR, celebrity management, press release distribution & brand building. 800+ brands trust us.',
  'PR Agency Mumbai, Best PR Agency India, Public Relations Mumbai, RV Rising Media, Press Release Distribution India, Celebrity Management Mumbai, Bollywood PR, Digital PR India, Mumbai Production House, Brand Building, Rahul Varun',
  NULL, 'https://rvrising.com/',
  'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1',
  NULL, NULL, NOW(), NOW()
),
(
  'about', 'About Us',
  'About RV Rising Media — Mumbai PR Agency Founded by Rahul Varun',
  'Learn about RV Rising Media — founded in 2017 by Bollywood journalist Rahul Varun (Rahul Mishra). 9+ years, 800+ clients, 500+ media tie-ups across India.',
  'About RV Rising Media, Rahul Varun Founder, Rahul Mishra Journalist, Mumbai PR Agency History, Bollywood Journalist Mumbai, RV Rising Story, PR Agency Founder Mumbai, The Filmy Charcha, Attention India',
  NULL, 'https://rvrising.com/about',
  'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1',
  NULL, NULL, NOW(), NOW()
),
(
  'services', 'Our Services',
  'PR & Media Services — Public Relations, Digital PR, Celebrity Management | RV Rising',
  'Full-service PR offerings from RV Rising Media — public relations, digital PR, celebrity management, press conferences, ad films and influencer marketing in India.',
  'PR Services Mumbai, Digital PR Services India, Celebrity Management Services, Press Release Distribution, Influencer Marketing Mumbai, Ad Film Production, Public Relations Services, Brand PR India, Press Conference Mumbai',
  NULL, 'https://rvrising.com/services',
  'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1',
  NULL, NULL, NOW(), NOW()
),
(
  'pr-services', 'PR Services',
  'PR Services in India — Get Featured in 500+ Media | Starting ₹999',
  'Get your brand featured in India''s top media — Times of India, ANI, Mid-Day, NewsX and 500+ outlets. Affordable PR packages from ₹999. 24-hour delivery.',
  'PR Services India, Press Release India, Cheap PR Packages, PR Pricing India, Mumbai PR Pricing, ANI PR, PTI PR, Times of India PR, Tier 1 PR India, Affordable PR India, PR Packages ₹999, Digital PR India',
  NULL, 'https://rvrising.com/pr-services',
  'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1',
  NULL, NULL, NOW(), NOW()
),
(
  'studio', 'Studio',
  'Podcast & Video Studio Mumbai — 4K Cinematic from ₹3,000/hr | RV Rising',
  'Pro podcast and video studio in Andheri West, Mumbai. 3-camera 4K setup, pro mics, lights and crew included. Book from ₹3,000/hour. Used by Bollywood talent.',
  'Podcast Studio Mumbai, Video Studio Andheri, Recording Studio Mumbai, Podcast Recording Mumbai, 4K Video Studio India, Bollywood Studio Mumbai, Studio Rental Mumbai, Podcast Studio Booking Mumbai, Oshiwara Studio',
  NULL, 'https://rvrising.com/studio',
  'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1',
  NULL, NULL, NOW(), NOW()
);

-- ----------------------------------------------------------------------------
-- 3. leads  (form submissions — dedupe + 2-day mail cooldown)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `leads` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `source` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'contact',
  `name` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `service` VARCHAR(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` VARCHAR(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` TEXT COLLATE utf8mb4_unicode_ci,
  `dedupe_key` CHAR(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mail_send` TINYINT(1) NOT NULL DEFAULT '0',
  `form_resubmit` TINYINT(1) NOT NULL DEFAULT '0',
  `resubmit_count` INT UNSIGNED NOT NULL DEFAULT '0',
  `last_mail_sent_at` DATETIME NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `leads_dedupe_key_unique` (`dedupe_key`),
  KEY `leads_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 4. local_seos  (city landing pages for home / pr-services / studio)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `local_seos` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `page_slug` VARCHAR(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` VARCHAR(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city_slug` VARCHAR(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_title` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` TEXT COLLATE utf8mb4_unicode_ci,
  `meta_keywords` TEXT COLLATE utf8mb4_unicode_ci,
  `og_image` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `canonical_override` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `robots` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `json_ld` TEXT COLLATE utf8mb4_unicode_ci,
  `custom_head` TEXT COLLATE utf8mb4_unicode_ci,
  `hero_heading` TEXT COLLATE utf8mb4_unicode_ci,
  `hero_subheading` TEXT COLLATE utf8mb4_unicode_ci,
  `faqs` TEXT COLLATE utf8mb4_unicode_ci,
  `is_active` TINYINT(1) NOT NULL DEFAULT '1',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `local_seos_page_slug_city_slug_unique` (`page_slug`,`city_slug`),
  KEY `local_seos_is_active_page_slug_index` (`is_active`,`page_slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 5. pr_packages  (PR pricing cards on /pr-services, managed via admin)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pr_packages` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `label` VARCHAR(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` VARCHAR(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_price` VARCHAR(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` VARCHAR(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sub` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `features` TEXT COLLATE utf8mb4_unicode_ci,
  `badge` VARCHAR(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_popular` TINYINT(1) NOT NULL DEFAULT '0',
  `sort_order` INT UNSIGNED NOT NULL DEFAULT '0',
  `is_active` TINYINT(1) NOT NULL DEFAULT '1',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pr_packages_name_unique` (`name`),
  KEY `pr_packages_is_active_sort_order_index` (`is_active`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed the six default packages (skipped if a package with that name already exists).
INSERT IGNORE INTO `pr_packages`
  (`label`, `name`, `original_price`, `price`, `sub`, `features`, `badge`, `is_popular`, `sort_order`, `is_active`, `created_at`, `updated_at`)
VALUES
  ('Starter',   'Trial Pack',      '1,999',  '999',    'First-timer? Try us with 200+ outlets.',     '["200+ Digital Outlets","Attention India · Daily Hunt","News18 Nation · Loktej","24-hour delivery"]',                 NULL,           0, 1, 1, NOW(), NOW()),
  ('Standard',  'Basic Plus 250',  '8,000',  '5,000',  'Wider reach with premium regional press.',   '["250+ Outlets","Lokmat Times · UNI","Ahmedabad Mirror · First India","Live coverage report"]',                     'Most Popular', 1, 2, 1, NOW(), NOW()),
  ('Premium',   'NewsX Plus',      '12,000', '7,500',  'High-authority national news network.',      '["NewsX + India News","The Daily Guardian","HT Syndication · My Nation","250+ supporting outlets"]',                 NULL,           0, 3, 1, NOW(), NOW()),
  ('Premium',   'ANI Plus',        '12,500', '7,800',  'Wire-syndicated coverage via ANI.',          '["ANI Wire syndication","100+ verified placements","Tier-1 digital coverage","Live tracking dashboard"]',            NULL,           0, 4, 1, NOW(), NOW()),
  ('Editorial', 'Midday Standard', '16,000', '10,500', 'Editorial placement on Mid-Day + regional.', '["Mid-Day editorial","Lokmat Times","250+ digital outlets","Premium narrative drafting"]',                          NULL,           0, 5, 1, NOW(), NOW()),
  ('Elite',     'ANI + BS + PTI',  '24,999', '16,500', 'Triple wire combo — maximum authority.',     '["ANI Wire","Business Standard","PTI Wire","400+ total placements"]',                                                NULL,           0, 6, 1, NOW(), NOW());

-- ----------------------------------------------------------------------------
-- 6. Register all migrations so `php artisan migrate` on production treats them
--    as already-applied and never tries to re-create these tables.
-- ----------------------------------------------------------------------------
INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
  ('2026_06_06_100001_create_testimonials_table',      (SELECT IFNULL(MAX(b), 1) FROM (SELECT MAX(batch) AS b FROM migrations) AS m)),
  ('2026_06_06_100002_create_page_seos_table',         (SELECT IFNULL(MAX(b), 1) FROM (SELECT MAX(batch) AS b FROM migrations) AS m)),
  ('2026_06_12_100001_create_leads_table',             (SELECT IFNULL(MAX(b), 1) FROM (SELECT MAX(batch) AS b FROM migrations) AS m)),
  ('2026_06_12_100002_create_local_seos_table',        (SELECT IFNULL(MAX(b), 1) FROM (SELECT MAX(batch) AS b FROM migrations) AS m)),
  ('2026_06_12_100003_add_city_to_testimonials_table', (SELECT IFNULL(MAX(b), 1) FROM (SELECT MAX(batch) AS b FROM migrations) AS m)),
  ('2026_06_12_100004_create_pr_packages_table',       (SELECT IFNULL(MAX(b), 1) FROM (SELECT MAX(batch) AS b FROM migrations) AS m));

-- ============================================================================
-- DONE. Verify with:
--   SHOW TABLES;                                  -- expect testimonials, page_seos, leads, local_seos
--   SHOW COLUMNS FROM testimonials LIKE 'city';   -- expect 1 row
--   SELECT page_slug, meta_title FROM page_seos;  -- expect 5 rows
--   SELECT COUNT(*) FROM local_seos;
--   SELECT name, price FROM pr_packages;          -- expect 6 rows
-- ============================================================================
