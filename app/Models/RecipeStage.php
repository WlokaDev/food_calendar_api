<?php

namespace App\Models;

use App\Interfaces\ChildRecipeAttributeInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

/**
 * @property string $description
 * @property integer $sort
 * @property-read Recipe $recipe
 */

class RecipeStage extends Model implements ChildRecipeAttributeInterface
{
    use HasTranslations, SoftDeletes;

    public $translatable = [
        'description',
    ];

    public function recipe() : BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    /**
     * @param int $id
     * @return void
     */

    public function setParentIdAttribute(int $id) : void
    {
        $this->attributes['recipe_id'] = $id;
    }
}
