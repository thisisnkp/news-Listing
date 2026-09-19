<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pr_packages', function (Blueprint $table) {
            $table->id();
            $table->string('label', 80)->nullable();       // small uppercase tier label (Starter / Premium…)
            $table->string('name', 160)->unique();          // card heading (Trial Pack…) — unique for idempotent seeding
            $table->string('original_price', 40)->nullable(); // struck-through dummy price (e.g. 1,999)
            $table->string('price', 40);                    // actual price (e.g. 999)
            $table->string('sub', 255)->nullable();         // one-line subtitle
            $table->text('features')->nullable();           // JSON array of bullet strings
            $table->string('badge', 80)->nullable();        // ribbon text (e.g. "Most Popular"); blank = none
            $table->boolean('is_popular')->default(false);  // dark highlighted card
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pr_packages');
    }
};
