<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profile_ownerships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('profile_id')->constrained('business_profiles')->cascadeOnDelete();
            $table->string('role')->default('owner');
            $table->timestamp('granted_at')->useCurrent();
            $table->foreignId('granted_via_claim_request_id')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'profile_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_ownerships');
    }
};
