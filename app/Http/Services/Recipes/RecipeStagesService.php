<?php

namespace App\Http\Services\Recipes;

use App\Models\Recipe;
use App\Models\RecipeStage;

class RecipeStagesService
{
    /**
     * @var RecipeStage
     */

    protected RecipeStage $recipeStage;

    public function __construct(?RecipeStage $recipeStage = null)
    {
        $this->recipeStage = $recipeStage ?: new RecipeStage();
    }

    /**
     * @param Recipe|int|null $recipe
     * @param string $description
     * @param int $sort
     * @param bool $save
     * @return $this
     */

    public function assignAttributes(
        Recipe|int|null $recipe,
        string $description,
        int $sort,
        bool $save = true
    ) : self
    {
        if($recipe) {
            $this->recipeStage->recipe()->associate($recipe);
        }

        $this->recipeStage->description = $description;
        $this->recipeStage->sort = $sort;

        if($save) {
            $this->recipeStage->save();
        }

        return $this;
    }

    /**
     * @return RecipeStage
     */

    public function getRecipeStage(): RecipeStage
    {
        return $this->recipeStage;
    }
}
