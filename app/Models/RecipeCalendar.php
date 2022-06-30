<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property-read int $id
 * @property Carbon $day
 * @property string $human_day
 * @property Recipe $recipe
 * @property User $user
 */

class RecipeCalendar extends Model
{
    use HasFactory;

    protected $casts = [
        'day' => 'date'
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recipe() : BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function category() : BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return string
     */

    public function getHumanDayAttribute() : string
    {
        return Str::of($this->day->locale(
            app()->getLocale()
        )->getTranslatedDayName())->ucfirst();
    }
}
