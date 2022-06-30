<?php

namespace App\Http\Services\Recipes;

use App\Enums\CurrencyEnum;
use App\Models\Recipe;

class RecipesService
{
    /**
     * @var Recipe
     */

    protected Recipe $recipe;

    /**
     * @param Recipe|null $recipe
     */

    public function __construct(?Recipe $recipe = null)
    {
        $this->recipe = $recipe ?: new Recipe();
    }

    /**
     * @param string $title
     * @param string $description
     * @param int $difficulty_of_execution
     * @param int $execution_time
     * @param float|null $price
     * @param CurrencyEnum|null $currencyEnum
     * @param int|null $calories
     * @param bool $save
     * @return $this
     */

    public function assignAttributes(
        string $title,
        string $description,
        int $difficulty_of_execution,
        int $execution_time,
        ?float $price = null,
        ?CurrencyEnum $currencyEnum = null,
        ?int $calories = null,
        bool $save = true
    ) : self
    {
        $this->recipe->title = $title;
        $this->recipe->description = $description;
        $this->recipe->difficulty_of_execution = $difficulty_of_execution;
        $this->recipe->execution_time = $execution_time;
        $this->recipe->price = $price;

        if(isset($currencyEnum)) {
            $this->recipe->currency = $currencyEnum->value;
        }

        $this->recipe->calories = $calories;

        if($save) {
            $this->recipe->save();
        }

        return $this;
    }

    /**
     * @return Recipe
     */

    public function getRecipe(): Recipe
    {
        return $this->recipe;
    }
}
