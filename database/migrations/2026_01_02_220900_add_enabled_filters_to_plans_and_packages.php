<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add enabled_filters to plans table
        Schema::table('plans', function (Blueprint $table) {
            $table->json('enabled_filters')->nullable()->after('order_button_link');
        });

        // Add enabled_filters to packages table (for media type)
        Schema::table('packages', function (Blueprint $table) {
            $table->json('enabled_filters')->nullable()->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('enabled_filters');
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('enabled_filters');
        });
    }
};
