<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
        ];
    }

    public function ownerships(): HasMany
    {
        return $this->hasMany(ProfileOwnership::class);
    }

    public function claimRequests(): HasMany
    {
        return $this->hasMany(ClaimRequest::class, 'requested_by_user_id');
    }

    public function reviewedClaims(): HasMany
    {
        return $this->hasMany(ClaimRequest::class, 'reviewed_by_staff_id');
    }

    public function submittedDisputes(): HasMany
    {
        return $this->hasMany(Dispute::class, 'submitted_by_user_id');
    }

    public function resolvedDisputes(): HasMany
    {
        return $this->hasMany(Dispute::class, 'resolved_by_staff_id');
    }
}
