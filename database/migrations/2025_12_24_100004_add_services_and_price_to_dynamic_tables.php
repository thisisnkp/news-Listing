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
        Schema::table('dynamic_tables', function (Blueprint $table) {
            $table->json('services')->nullable()->after('slug');
            $table->decimal('price', 10, 2)->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dynamic_tables', function (Blueprint $table) {
            $table->dropColumn(['services', 'price']);
        });
    }
};
