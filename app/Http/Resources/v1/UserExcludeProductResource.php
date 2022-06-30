<?php

namespace App\Http\Resources\v1;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use function route;

/**
 * @mixin Product
 */

class UserExcludeProductResource extends JsonResource
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
            'product_name' => $this->name,
            'image_path' => route('products.image', $this->id)
        ];
    }
}
