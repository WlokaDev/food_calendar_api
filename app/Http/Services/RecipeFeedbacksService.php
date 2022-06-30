<?php

namespace App\Http\Services;

use App\Models\Recipe;
use App\Models\RecipeFeedback;
use Illuminate\Support\Facades\Auth;

class RecipeFeedbacksService
{
    /**
     * @var RecipeFeedback
     */

    protected RecipeFeedback $recipeFeedback;

    /**
     * @param RecipeFeedback|null $recipeFeedback
     */

    public function __construct(?RecipeFeedback $recipeFeedback = null)
    {
        $this->recipeFeedback = $recipeFeedback ?: new RecipeFeedback();
    }

    /**
     * @param Recipe $recipe
     * @param string|null $feedback
     * @param int|null $feedbackStar
     * @param bool $save
     * @return $this
     */

    public function assignAttributes(
        Recipe $recipe,
        ?string $feedback = null,
        ?int $feedbackStar = null,
        bool $save = true
    ) : self
    {
        $this->recipeFeedback->feedback = $feedback;
        $this->recipeFeedback->feedback_star = $feedbackStar;
        $this->recipeFeedback->user_id = Auth::id();
        $this->recipeFeedback->recipe_id = $recipe;

        if($save) {
            $this->recipeFeedback->save();
        }

        return $this;
    }

    /**
     * @return RecipeFeedback
     */

    public function getRecipeFeedback(): RecipeFeedback
    {
        return $this->recipeFeedback;
    }
}
