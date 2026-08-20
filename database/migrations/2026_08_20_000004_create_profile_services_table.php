<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profile_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('business_profiles')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('price')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_services');
    }
};
