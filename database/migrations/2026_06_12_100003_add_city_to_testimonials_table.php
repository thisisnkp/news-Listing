<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            // City tag so a testimonial can be surfaced on that city's local-SEO page.
            $table->string('city', 160)->nullable()->after('company');
            $table->index('city');
        });
    }

    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropIndex(['city']);
            $table->dropColumn('city');
        });
    }
};
