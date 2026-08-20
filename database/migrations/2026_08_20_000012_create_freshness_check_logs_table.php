<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('freshness_check_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('business_profiles')->cascadeOnDelete();
            $table->foreignId('data_source_id')->nullable()->constrained('data_sources')->nullOnDelete();
            $table->timestamp('checked_at')->useCurrent();
            $table->json('discrepancies');
            $table->string('severity');
            $table->boolean('alert_sent')->default(false);
            $table->timestamp('alert_sent_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->string('resolution_action')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('freshness_check_logs');
    }
};
