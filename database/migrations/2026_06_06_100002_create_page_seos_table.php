<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_seos', function (Blueprint $table) {
            $table->id();
            // page_slug matches the .php filename without extension on the main site,
            // e.g. "home" (=index.php), "about", "services", "pr-services", "studio".
            $table->string('page_slug')->unique();
            $table->string('page_label');                   // human-friendly admin name
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('og_image')->nullable();          // storage path
            $table->string('canonical_override')->nullable();
            $table->string('robots')->nullable();            // e.g. "index, follow"
            $table->text('json_ld')->nullable();             // raw JSON-LD block (optional)
            $table->text('custom_head')->nullable();         // extra <head> snippet
            $table->timestamps();
        });

        // Seed the five managed pages so admin lands on a populated list immediately.
        $now = now();
        \DB::table('page_seos')->insert([
            ['page_slug' => 'home',        'page_label' => 'Home',         'created_at' => $now, 'updated_at' => $now],
            ['page_slug' => 'about',       'page_label' => 'About Us',     'created_at' => $now, 'updated_at' => $now],
            ['page_slug' => 'services',    'page_label' => 'Our Services', 'created_at' => $now, 'updated_at' => $now],
            ['page_slug' => 'pr-services', 'page_label' => 'PR Services',  'created_at' => $now, 'updated_at' => $now],
            ['page_slug' => 'studio',      'page_label' => 'Studio',       'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('page_seos');
    }
};
