<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingestion_source_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 2);
            $table->string('source_type');
            $table->timestamp('last_run_at')->nullable();
            $table->string('last_run_status')->nullable();
            $table->unsignedInteger('records_ingested')->default(0);
            $table->unsignedInteger('records_failed')->default(0);
            $table->timestamp('next_scheduled_run')->nullable();
            $table->timestamps();

            $table->unique(['country_code', 'source_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingestion_source_statuses');
    }
};
