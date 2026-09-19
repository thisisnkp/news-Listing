<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('local_seos', function (Blueprint $table) {
            $table->id();
            $table->string('page_slug', 32);             // home | pr-services | studio
            $table->string('city', 160);                 // display name, e.g. "Mumbai"
            $table->string('city_slug', 160);            // url segment, e.g. "mumbai"

            // SEO meta (same shape as page_seos)
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('og_image')->nullable();
            $table->string('canonical_override')->nullable();
            $table->string('robots')->nullable();
            $table->text('json_ld')->nullable();
            $table->text('custom_head')->nullable();

            // Overridable visible sections (everything else inherits from the base page)
            $table->text('hero_heading')->nullable();    // H1 (HTML allowed — admin-trusted)
            $table->text('hero_subheading')->nullable(); // lead paragraph (HTML allowed)
            $table->text('faqs')->nullable();            // JSON array of {q, a}

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['page_slug', 'city_slug']);
            $table->index(['is_active', 'page_slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('local_seos');
    }
};
