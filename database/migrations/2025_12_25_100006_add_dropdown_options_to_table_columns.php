<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('table_columns', function (Blueprint $table) {
            $table->json('dropdown_options')->nullable()->after('name_if_button');
        });
    }

    public function down(): void
    {
        Schema::table('table_columns', function (Blueprint $table) {
            $table->dropColumn('dropdown_options');
        });
    }
};
