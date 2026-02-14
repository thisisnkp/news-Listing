<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('table_columns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dynamic_table_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->enum('type', ['text', 'number', 'currency', 'button'])->default('text');
            $table->boolean('is_translatable')->default(false);
            $table->boolean('is_filterable')->default(false);
            $table->boolean('is_sortable')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->unique(['dynamic_table_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('table_columns');
    }
};
