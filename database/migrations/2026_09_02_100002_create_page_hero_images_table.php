<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Images for the auto-scrolling strip in the hero (home / pr-services).
        Schema::create('page_hero_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_seo_id')->constrained('page_seos')->cascadeOnDelete();
            $table->string('image');                    // storage path
            $table->string('alt')->nullable();          // alt text for the first (visible) copy
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['page_seo_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_hero_images');
    }
};
