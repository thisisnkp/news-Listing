<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['text', 'video'])->default('text');
            $table->string('name');
            $table->string('role')->nullable();
            $table->string('company')->nullable();
            $table->text('message')->nullable();
            $table->string('image')->nullable();             // avatar path (storage)
            $table->string('video_url')->nullable();         // YouTube / Vimeo embed URL
            $table->unsignedTinyInteger('rating')->default(5); // 0–5
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
