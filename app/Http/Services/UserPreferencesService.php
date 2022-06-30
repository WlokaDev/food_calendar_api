<?php

namespace App\Http\Services;

use App\Models\UserPreference;
use Illuminate\Support\Facades\Auth;

class UserPreferencesService
{
    /**
     * @var UserPreference
     */

    protected UserPreference $userPreference;

    public function __construct()
    {
        $this->userPreference = Auth::user()->preferences ?: new UserPreference([
            'user_id' => Auth::id()
        ]);
    }

    /**
     * @param float|null $minPrice
     * @param float|null $maxPrice
     * @param int|null $minCalories
     * @param int|null $maxCalories
     * @param int|null $minDifficultyOfExecution
     * @param int|null $maxDifficultyOfExecution
     * @param int|null $minExecutionTime
     * @param int|null $maxExecutionTime
     * @return $this
     */

    public function assignAttributes(
        ?float $minPrice = null,
        ?float $maxPrice = null,
        ?int $minCalories = null,
        ?int $maxCalories = null,
        ?int $minDifficultyOfExecution = null,
        ?int $maxDifficultyOfExecution = null,
        ?int $minExecutionTime = null,
        ?int $maxExecutionTime = null
    ) : self
    {
        $this->userPreference->min_price = $minPrice;
        $this->userPreference->max_price = $maxPrice;
        $this->userPreference->min_calories = $minCalories;
        $this->userPreference->max_calories = $maxCalories;
        $this->userPreference->min_difficulty_of_execution = $minDifficultyOfExecution;
        $this->userPreference->max_difficulty_of_execution = $maxDifficultyOfExecution;
        $this->userPreference->min_execution_time = $minExecutionTime;
        $this->userPreference->max_execution_time = $maxExecutionTime;

        $this->userPreference->save();

        return $this;
    }

    /**
     * @return UserPreference
     */

    public function getUserPreference(): UserPreference
    {
        return $this->userPreference;
    }
}
