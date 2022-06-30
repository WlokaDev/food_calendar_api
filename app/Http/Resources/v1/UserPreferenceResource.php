<?php

namespace App\Http\Resources\v1;

use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin UserPreference
 */

class UserPreferenceResource extends JsonResource
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
            'min_price' => $this->min_price,
            'max_price' => $this->max_price,
            'min_calories' => $this->min_calories,
            'max_calories' => $this->max_calories,
            'min_difficulty_of_execution' => $this->min_difficulty_of_execution,
            'max_difficulty_of_execution' => $this->max_difficulty_of_execution,
            'min_execution_time' => $this->min_execution_time,
            'max_execution_time' => $this->max_execution_time
        ];
    }
}
