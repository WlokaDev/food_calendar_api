<?php

namespace App\Http\Services\Recipes;

use App\Models\Category;
use App\Models\Recipe;
use App\Models\RecipeCategory;

class RecipeCategoriesService
{
    /**
     * @var RecipeCategory
     */

    protected RecipeCategory $recipeCategory;

    /**
     * @param RecipeCategory|null $recipeCategory
     */

    public function __construct(?RecipeCategory $recipeCategory = null)
    {
        $this->recipeCategory = $recipeCategory ?: new RecipeCategory();
    }

    /**
     * @param Recipe|int|null $recipe
     * @param Category $category
     * @param bool $save
     * @return $this
     */

    public function assignAttributes(
        Recipe|int|null $recipe,
        Category $category,
        bool $save = false
    ) : self
    {
        if($recipe) {
            $this->recipeCategory->recipe()->associate($recipe);
        }

        $this->recipeCategory->category()->associate($category);

        if($save) {
            $this->recipeCategory->save();
        }

        return $this;
    }
}
