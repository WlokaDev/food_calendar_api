<?php

namespace App\Http\Resources\v1;

use App\Models\RecipeCalendar;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin RecipeCalendar
 */

class RecipeCalendarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'day' => $this->day->format('d.m.Y'),
            'human_day' => $this->human_day,
            'recipe' => new RecipeResource($this->recipe)
        ];
    }
}
