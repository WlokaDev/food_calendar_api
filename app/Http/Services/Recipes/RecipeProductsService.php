<?php

namespace App\Http\Services\Recipes;

use App\Enums\UnitEnum;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\RecipeProduct;

class RecipeProductsService
{
    /**
     * @var RecipeProduct
     */

    protected RecipeProduct $recipeProduct;

    public function __construct(?RecipeProduct $recipeProduct = null)
    {
        $this->recipeProduct = $recipeProduct ?: new RecipeProduct();
    }

    /**
     * @param Recipe|int|null $recipe
     * @param Product|int $product
     * @param UnitEnum $unitEnum
     * @param string $value
     * @param bool $save
     * @return $this
     */

    public function assignAttributes(
        Recipe|int|null $recipe,
        Product|int $product,
        UnitEnum $unitEnum,
        string $value,
        bool $save = true
    ) : self
    {
        if($recipe) {
            $this->recipeProduct->product()->associate($product);
        }

        $this->recipeProduct->product()->associate($product);
        $this->recipeProduct->unit = $unitEnum->value;
        $this->recipeProduct->value = $value;

        if($save) {
            $this->recipeProduct->save();
        }

        return $this;
    }

    /**
     * @return RecipeProduct
     */

    public function getRecipeProduct(): RecipeProduct
    {
        return $this->recipeProduct;
    }
}
