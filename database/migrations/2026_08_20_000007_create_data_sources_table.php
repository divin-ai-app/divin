<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('business_profiles')->cascadeOnDelete();
            $table->string('source_type');
            $table->string('source_url')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->json('current_snapshot');
            $table->string('coherence_status')->default('not_checked');
            $table->timestamps();

            $table->index('source_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_sources');
    }
};
