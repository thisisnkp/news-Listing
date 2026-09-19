<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_seos', function (Blueprint $table) {
            // Master switch — while off, the page keeps its hardcoded hero copy.
            $table->boolean('hero_enabled')->default(false)->after('page_label');
            $table->string('hero_badge')->nullable()->after('hero_enabled');       // small pill above the H1
            $table->text('hero_heading')->nullable()->after('hero_badge');         // H1, HTML allowed
            $table->text('hero_subheading')->nullable()->after('hero_heading');    // lead paragraph, HTML allowed
            $table->string('hero_primary_label')->nullable()->after('hero_subheading');
            $table->string('hero_primary_url')->nullable()->after('hero_primary_label');
            $table->string('hero_secondary_label')->nullable()->after('hero_primary_url');
            $table->string('hero_secondary_url')->nullable()->after('hero_secondary_label');
            // default = keep the page's built-in visual; image | video (upload) | embed (external URL)
            $table->string('hero_media_type', 20)->default('default')->after('hero_secondary_url');
            $table->string('hero_image')->nullable()->after('hero_media_type');    // storage path — also the video poster
            $table->string('hero_video')->nullable()->after('hero_image');         // storage path (mp4/webm)
            $table->string('hero_video_url')->nullable()->after('hero_video');     // YouTube / Vimeo / direct mp4
        });
    }

    public function down(): void
    {
        Schema::table('page_seos', function (Blueprint $table) {
            $table->dropColumn([
                'hero_enabled', 'hero_badge', 'hero_heading', 'hero_subheading',
                'hero_primary_label', 'hero_primary_url',
                'hero_secondary_label', 'hero_secondary_url',
                'hero_media_type', 'hero_image', 'hero_video', 'hero_video_url',
            ]);
        });
    }
};
