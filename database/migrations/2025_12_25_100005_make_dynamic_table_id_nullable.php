<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Make dynamic_table_id nullable in table_columns
        Schema::table('table_columns', function (Blueprint $table) {
            $table->unsignedBigInteger('dynamic_table_id')->nullable()->change();
        });

        // Make dynamic_table_id nullable in table_rows
        Schema::table('table_rows', function (Blueprint $table) {
            $table->unsignedBigInteger('dynamic_table_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('table_columns', function (Blueprint $table) {
            $table->unsignedBigInteger('dynamic_table_id')->nullable(false)->change();
        });

        Schema::table('table_rows', function (Blueprint $table) {
            $table->unsignedBigInteger('dynamic_table_id')->nullable(false)->change();
        });
    }
};
