<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_buttons', function (Blueprint $table) {
            $table->id();
            $table->string('label', 160);                 // button text
            $table->string('icon', 80)->nullable();        // Font Awesome class e.g. "fas fa-newspaper"
            $table->string('url', 500);                    // where the button links
            $table->boolean('new_tab')->default(false);    // open in a new tab?
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_buttons');
    }
};
