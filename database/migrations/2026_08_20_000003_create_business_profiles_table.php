<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('canonical_id')->unique();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('legal_name')->nullable();
            $table->string('industry');
            $table->string('category');
            $table->string('country_code', 2);
            $table->string('city');
            $table->string('address_line1');
            $table->string('address_line2')->nullable();
            $table->string('postal_code')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('phone')->nullable();
            $table->string('public_email')->nullable();
            $table->string('website')->nullable();
            $table->text('description_short');
            $table->text('description_long')->nullable();
            $table->json('hours')->nullable();
            $table->string('price_range')->nullable();
            $table->string('status')->default('draft');
            $table->string('claim_status')->default('unclaimed');
            $table->string('plan_tier')->default('none');
            $table->timestamp('last_verified_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->foreign('country_code')->references('country_code')->on('country_clearances');
            $table->index('industry');
            $table->index('status');
            $table->index('claim_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_profiles');
    }
};
