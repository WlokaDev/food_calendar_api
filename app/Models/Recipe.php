<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

/**
 * @property string $title
 * @property string $description
 * @property ?float $price
 * @property ?string $currency
 * @property ?int $calories
 * @property int $difficulty_of_execution
 * @property int $execution_time
 * @property-read User $author
 * @property-read Collection|RecipeStage[] $stages
 * @property-read Collection|RecipeProduct[] $recipeProducts
 * @property-read Collection|Image[] $images
 * @property-read Collection|Product[] $products
 * @property-read Collection|RecipeFeedback[] $feedbacks
 */

class Recipe extends Model
{
    use HasTranslations, SoftDeletes;

    public array $translatable = [
        'description',
        'title'
    ];

    protected static function boot()
    {
        self::deleted(static function (self $recipe) {
            $recipe->stages()->delete();
            $recipe->products()->delete();
            $recipe->images()->delete();
        });

        parent::boot();
    }

    public function author() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function stages() : HasMany
    {
        return $this->hasMany(RecipeStage::class);
    }

    public function recipeProducts() : HasMany
    {
        return $this->hasMany(RecipeProduct::class);
    }

    public function products() : HasManyThrough
    {
        return $this->hasManyThrough(Product::class, RecipeProduct::class);
    }

    public function images() : MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function feedbacks() : HasMany
    {
        return $this->hasMany(RecipeFeedback::class);
    }

    public function categories() : HasManyThrough
    {
        return $this->hasManyThrough(Category::class, RecipeCategory::class);
    }

    public function recipeCategories() : HasMany
    {
        return $this->hasMany(RecipeCategory::class);
    }

    public function calendars() : HasMany
    {
        return $this->hasMany(RecipeCalendar::class);
    }

    /**
     * @return Image|null
     */

    public function mainImage() : ?Image
    {
        return $this
            ->images()
            ->where('main', true)
            ->first();
    }
}
