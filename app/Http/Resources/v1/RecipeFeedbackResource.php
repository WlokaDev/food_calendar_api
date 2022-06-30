<?php

namespace App\Http\Resources\v1;

use App\Models\RecipeFeedback;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin RecipeFeedback
 */

class RecipeFeedbackResource extends JsonResource
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
            'feedback' => $this->feedback,
            'feedback_star' => $this->feedback_star,
            'user' => new AuthorResource(
                $this->user
            ),
            'images' => ImageResource::collection(
                $this->images
            ),
            'created_at' => $this->created_at->diffForHumans()
        ];
    }
}
