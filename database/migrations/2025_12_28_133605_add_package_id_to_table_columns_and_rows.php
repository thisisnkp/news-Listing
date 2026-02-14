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
        Schema::table('table_columns', function (Blueprint $table) {
            $table->foreignId('package_id')->nullable()->after('plan_id')->constrained()->onDelete('cascade');
        });

        Schema::table('table_rows', function (Blueprint $table) {
            $table->foreignId('package_id')->nullable()->after('plan_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('table_columns', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
            $table->dropColumn('package_id');
        });

        Schema::table('table_rows', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
            $table->dropColumn('package_id');
        });
    }
};
