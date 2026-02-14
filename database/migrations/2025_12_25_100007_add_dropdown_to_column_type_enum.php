<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Modify the type column to include 'dropdown'
        DB::statement("ALTER TABLE table_columns MODIFY COLUMN type ENUM('text', 'number', 'currency', 'button', 'dropdown') NOT NULL DEFAULT 'text'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE table_columns MODIFY COLUMN type ENUM('text', 'number', 'currency', 'button') NOT NULL DEFAULT 'text'");
    }
};
