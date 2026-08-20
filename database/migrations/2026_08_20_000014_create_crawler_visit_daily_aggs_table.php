<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crawler_visit_daily_aggs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('business_profiles')->cascadeOnDelete();
            $table->date('date');
            $table->string('bot_name');
            $table->unsignedInteger('visit_count')->default(0);
            $table->timestamps();

            $table->unique(['profile_id', 'date', 'bot_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crawler_visit_daily_aggs');
    }
};
