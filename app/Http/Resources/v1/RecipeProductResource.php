<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */

    public function toArray($request) : array
    {
        return [
            'product_name' => $this->name,
            'unit' => $this->pivot->unit,
            'value' => $this->pivot->value
        ];
    }
}
