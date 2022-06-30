<?php

namespace App\Models;


use App\Interfaces\ChildRecipeAttributeInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $unit
 * @property string $value
 * @property-read Product $product
 * @property-read Recipe $recipe
 */

class RecipeProduct extends Model implements ChildRecipeAttributeInterface
{
    use SoftDeletes;

    public function product() : BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function recipe() : BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function setParentIdAttribute(int $id) : void
    {
        $this->attributes['recipe_id'] = $id;
    }
}
