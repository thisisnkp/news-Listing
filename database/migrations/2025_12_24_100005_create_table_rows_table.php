<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('table_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dynamic_table_id')->constrained()->cascadeOnDelete();
            $table->json('data'); // Non-translatable column data
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('table_rows');
    }
};
