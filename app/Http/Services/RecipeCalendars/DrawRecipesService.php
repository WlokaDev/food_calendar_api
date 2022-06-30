<?php

namespace App\Http\Services\RecipeCalendars;

use App\Models\Recipe;
use App\Models\RecipeCalendar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class DrawRecipesService
{
    /**
     * @var int
     */

    protected int $days;

    /**
     * @var string|null
     */

    protected ?string $date;

    /**
     * @var RecipeCalendar[]
     */

    protected array $drawnRecipes = [];

    /**
     * @var int[]
     */

    protected array $categoryIds;

    /**
     * @param array $categoryIds
     * @param int $days
     * @param string|null $date
     */

    public function __construct(array $categoryIds, int $days = 1, ?string $date = null)
    {
        $this->days = $days;
        $this->date = $date;
        $this->categoryIds = $categoryIds;
    }

    /**
     * @return RecipeCalendar[]
     */

    public function drawRecipes() : array
    {
        if($this->date && $this->days !== 1) {
            throw new \RuntimeException('Unable to draw recipe with filled days more than 1 and date');
        }

        foreach($this->categoryIds as $categoryId) {
            for ($i = 0; $i < $this->days; $i++) {
                $recipeCalendar = new RecipeCalendar();
                $recipeCalendar->category()->associate($categoryId);
                $recipeCalendar->day = now()->addDays($i)->format('Y-m-d');
                $recipeCalendar->recipe()->associate(
                    $this->draw($categoryId)
                );
                $recipeCalendar->user()->associate(
                    Auth::user()
                );
                $recipeCalendar->save();

                $this->drawnRecipes[] = $recipeCalendar;
            }
        }

        return $this->drawnRecipes;
    }

    /**
     * @param int $categoryId
     * @return Recipe
     */

    private function draw(int $categoryId) : Recipe
    {
        return Recipe::query()
            ->when(Auth::user()->preferences, function(Builder $q) {
                return $q
                    ->when(Auth::user()->preferences->min_price, function(Builder $q) {
                        return $q->where('price', '>=', Auth::user()->preferences->min_price);
                    })
                    ->when(Auth::user()->preferences->max_price, function(Builder $q) {
                        return $q->where('price', '<=', Auth::user()->preferences->max_price);
                    })
                    ->when(Auth::user()->preferences->min_calories, function(Builder $q) {
                        return $q->where('calories', '>=', Auth::user()->preferences->min_calories);
                    })
                    ->when(Auth::user()->preferences->max_calories, function(Builder $q) {
                        return $q->where('calories', '<=', Auth::user()->preferences->max_calories);
                    })
                    ->when(Auth::user()->preferences->min_difficulty_of_execution, function(Builder $q) {
                        return $q->where('difficulty_of_execution', '>=', Auth::user()->preferences->min_difficulty_of_execution);
                    })
                    ->when(Auth::user()->preferences->max_difficulty_of_execution, function(Builder $q) {
                        return $q->where('difficulty_of_execution', '<=', Auth::user()->preferences->max_difficulty_of_execution);
                    })
                    ->when(Auth::user()->preferences->min_execution_time, function(Builder $q) {
                        return $q->where('execution_time', '>=', Auth::user()->preferences->min_execution_time);
                    })
                    ->when(Auth::user()->preferences->max_execution_time, function(Builder $q) {
                        return $q->where('execution_time', '<=', Auth::user()->preferences->max_execution_time);
                    });
            })->whereHas('recipeCategories', function(Builder $q) use($categoryId) {
                return $q->where('id', $categoryId);
            })->doesntHave('calendars', callback: function(Builder $q) {
                return $q
                    ->whereBetween('day', [
                        now()->subDays(5)->format('Y-m-d'),
                        now()->format('Y-m-d')
                    ])
                    ->where('user_id', Auth::id());
            })->firstOrFail();
    }
}
