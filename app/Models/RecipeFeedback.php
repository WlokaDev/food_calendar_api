<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property ?string $feedback
 * @property ?int $feedback_star
 * @property-read Recipe $recipe
 * @property-read Collection|Image[] $images
 * @property-read ?User $user
 * @property ?int $user_id
 * @property ?int $recipe_id
 */

class RecipeFeedback extends Model
{
    use HasFactory;

    public function recipe() : BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function images() : MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
