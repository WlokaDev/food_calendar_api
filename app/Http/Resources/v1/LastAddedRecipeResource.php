<?php

namespace App\Http\Resources\v1;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Recipe
 */

class LastAddedRecipeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'calories_amount' => $this->calories,
            'execution_time' => $this->execution_time,
            'average_feedback' => round($this->feedbacks_avg_feedback_star, 2),
            'image' => new ImageResource(
                $this->images->first()
            )
        ];
    }
}
