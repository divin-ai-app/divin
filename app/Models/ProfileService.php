<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['profile_id', 'name', 'description', 'price', 'sort_order'])]
class ProfileService extends Model
{
    use HasFactory;

    public function profile(): BelongsTo
    {
        return $this->belongsTo(BusinessProfile::class, 'profile_id');
    }
}
