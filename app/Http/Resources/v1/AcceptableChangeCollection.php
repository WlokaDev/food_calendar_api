<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AcceptableChangeCollection extends ResourceCollection
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
            'data' => AcceptableChangeResource::collection($this->collection),
            'pagination' => new PaginateResource($this)
        ];
    }
}
