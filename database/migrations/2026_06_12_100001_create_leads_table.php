<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('source')->default('contact');   // contact / popup / studio ...
            $table->string('name');
            $table->string('phone');
            $table->string('email');
            $table->string('service')->nullable();
            $table->string('subject')->nullable();
            $table->text('message')->nullable();

            // De-duplication: md5(normalized_phone|lower_email). One row per person.
            $table->char('dedupe_key', 32)->unique();

            // Mail / resubmit tracking (managed by the PHP form handler).
            $table->boolean('mail_send')->default(false);        // 1 once the lead mail has gone out this cycle
            $table->boolean('form_resubmit')->default(false);    // 1 if re-submitted within the cooldown
            $table->unsignedInteger('resubmit_count')->default(0);
            $table->dateTime('last_mail_sent_at')->nullable();   // drives the 2-day cooldown

            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
