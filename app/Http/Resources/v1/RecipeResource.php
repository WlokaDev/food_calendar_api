<?php

namespace App\Http\Resources\v1;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Recipe
 */
class RecipeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'currency' => $this->currency,
            'calories' => $this->calories,
            'difficulty_of_execution' => $this->difficulty_of_execution,
            'execution_time' => $this->execution_time,
            'author' => new AuthorResource(
                $this->whenLoaded('author')
            ),
            'stages' => RecipeStageResource::collection(
                $this->whenLoaded('stages')
            ),
            'products' => RecipeProductResource::collection(
                $this->whenLoaded('products')
            ),
            'images' => $this->whenLoaded(
                'images',
                ImageResource::collection(
                    $this->images
                ),
                new ImageResource(
                    $this->images()->first()
                )
            ),
            'created_at' => $this->created_at->format('d.m.Y H:i')
        ];
    }
}
