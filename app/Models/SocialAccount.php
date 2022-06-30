<?php

namespace App\Models;

use App\Enums\SocialAuthProviderEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $provider_name
 * @property string $provider_id
 * @property-read User $user
 */

class SocialAccount extends Model
{
    use HasFactory;

    protected $casts = [
        'provider_name' => SocialAuthProviderEnum::class
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
