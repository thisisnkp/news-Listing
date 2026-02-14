<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('row_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('table_row_id')->constrained()->cascadeOnDelete();
            $table->foreignId('language_id')->constrained()->cascadeOnDelete();
            $table->json('translated_data'); // name, remark translations
            $table->timestamps();

            $table->unique(['table_row_id', 'language_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('row_translations');
    }
};
