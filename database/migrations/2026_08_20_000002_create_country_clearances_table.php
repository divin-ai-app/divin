<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('country_clearances', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 2)->unique();
            $table->string('country_name');
            $table->string('legal_status')->default('not_started');
            $table->boolean('gdpr_excluded')->default(false);
            $table->text('notes')->nullable();
            $table->timestamp('cleared_at')->nullable();
            $table->foreignId('reviewed_by_staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('country_clearances');
    }
};
