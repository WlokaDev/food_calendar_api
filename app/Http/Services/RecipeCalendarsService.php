<?php

namespace App\Http\Services;

use App\Models\Recipe;
use App\Models\RecipeCalendar;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RecipeCalendarsService
{
    /**
     * @var RecipeCalendar
     */

    protected RecipeCalendar $recipeCalendar;

    /**
     * @param RecipeCalendar|null $recipeCalendar
     */

    public function __construct(?RecipeCalendar $recipeCalendar = null)
    {
        $this->recipeCalendar = $recipeCalendar ?: new RecipeCalendar();
    }

    /**
     * @param Recipe|int $recipe
     * @param Carbon $day
     * @return $this
     */

    public function assignAttributes(
        Recipe|int $recipe,
        Carbon $day
    ) : self
    {
        $this->recipeCalendar->recipe()->associate($recipe);
        $this->recipeCalendar->user()->associate(Auth::user());
        $this->recipeCalendar->day = $day->toDate();
        $this->recipeCalendar->save();

        return $this;
    }

    /**
     * @return RecipeCalendar
     */

    public function getRecipeCalendar(): RecipeCalendar
    {
        return $this->recipeCalendar;
    }
}
