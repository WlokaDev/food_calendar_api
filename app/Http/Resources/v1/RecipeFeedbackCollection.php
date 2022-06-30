<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class RecipeFeedbackCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */

    public function toArray($request) : array
    {
        return [
            'data' => RecipeFeedbackResource::collection($this->collection),
            'pagination' => new PaginateResource($this)
        ];
    }
}
