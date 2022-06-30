<?php

namespace App\Http\Resources\v1;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @mixin ProductCategory
 */

class ProductCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */

    #[ArrayShape([
        'id' => "int",
        'name_translation' => "\Illuminate\Http\Resources\MissingValue|mixed",
        'description_translation' => "\Illuminate\Http\Resources\MissingValue|mixed"
    ])]

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name_translation' => $this->whenLoaded(
                'nameTranslations',
                TranslationResource::collection(
                    $this->nameTranslations
                ),
                new TranslationResource(
                    $this->localeNameTranslation()
                )
            ),
            'description_translation' => $this->whenLoaded(
                'descriptionTranslations',
                TranslationResource::collection(
                    $this->descriptionTranslations
                ),
                new TranslationResource(
                    $this->localeDescriptionTranslation()
                )
            )
        ];
    }
}
