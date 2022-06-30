<?php

namespace App\Models;

use App\Enums\CategorySourceEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

/**
 * @property string $name
 * @property string $icon_path
 */

class Category extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = [
        'name'
    ];

    protected $casts = [
        'source' => CategorySourceEnum::class
    ];

    public function recipeCalendars() : HasMany
    {
        return $this->hasMany(RecipeCalendar::class);
    }

    public function recipeCategories() : HasMany
    {
        return $this->hasMany(RecipeCategory::class);
    }
}
