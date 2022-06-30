<?php

namespace App\Models;

use App\Interfaces\ChildRecipeAttributeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeCategory extends Model implements ChildRecipeAttributeInterface
{
    use HasFactory;

    public function category() : BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function recipe() : BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function setParentIdAttribute(int $id): void
    {
        $this->attributes['recipe_id'] = $id;
    }
}
