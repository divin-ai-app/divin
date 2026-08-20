<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Raw, high-volume, short retention — see plan §5. A scheduled job
        // rolls this up into crawler_visit_daily_aggs, which the dashboard
        // (Phase 6) actually queries.
        Schema::create('crawler_visit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('business_profiles')->cascadeOnDelete();
            $table->string('bot_name');
            $table->string('path');
            $table->text('user_agent');
            $table->string('ip_hash');
            $table->timestamp('timestamp')->useCurrent();

            $table->index(['profile_id', 'timestamp']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crawler_visit_logs');
    }
};
