<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable(['email', 'token_hash', 'expires_at', 'used_at'])]
class LoginLink extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }

    /** Creates a link, returning [model, rawToken] — only the hash is persisted. */
    public static function issue(string $email, int $minutesValid = 15): array
    {
        $rawToken = Str::random(48);

        $link = self::query()->create([
            'email' => $email,
            'token_hash' => hash('sha256', $rawToken),
            'expires_at' => now()->addMinutes($minutesValid),
        ]);

        return [$link, $rawToken];
    }

    public static function findValidByRawToken(string $rawToken): ?self
    {
        return self::query()
            ->where('token_hash', hash('sha256', $rawToken))
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();
    }

    public function markUsed(): void
    {
        $this->forceFill(['used_at' => now()])->save();
    }
}
