<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Passwordless account login (separate from claim-flow ownership
     * verification — see ClaimRequest/otp_hash). Built on a dedicated,
     * single-use, hashed token rather than Laravel's stateless signed URLs,
     * so a link can be invalidated after one use instead of remaining valid
     * (replayable) until it simply expires.
     */
    public function up(): void
    {
        Schema::create('login_links', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('token_hash')->unique();
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_links');
    }
};
