<?php

namespace App\Http\Requests;

use App\Enums\CurrencyEnum;
use App\Enums\UnitEnum;
use App\Rules\ArrayUniqueValue;
use Illuminate\Foundation\Http\FormRequest;

class StoreRecipeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:50'
            ],
            'description' => [
                'required',
                'string',
                'max:1000'
            ],
            'price' => [
                'nullable',
                'numeric',
                'min:0'
            ],
            'currency' => [
                'required_with:price',
                'string',
                'in:' . implode(',', CurrencyEnum::values())
            ],
            'calories' => [
                'nullable',
                'integer',
                'min:1'
            ],
            'difficulty_of_execution' => [
                'required',
                'min:1',
                'max:10',
                'integer'
            ],
            'execution_time' => [
                'required',
                'min:1',
                'integer'
            ],
            'stages' => [
                'required',
                'array',
                'min:1'
            ],
            'stages.*.sort' => [
                'required',
                'min:0',
                'integer'
            ],
            'stages.*.description' => [
                'required',
                'string',
                'max:1000'
            ],
            'images' => [
                'nullable',
                'array',
                new ArrayUniqueValue(true, 'main')
            ],
            'images.*.file' => [
                'image',
                'required',
                'max:10000'
            ],
            'images.*.main' => [
                'bool',
                'required',
            ],
            'products' => [
                'nullable',
                'array'
            ],
            'products.*.id' => [
                'required_with:products',
                'exists:products,id'
            ],
            'products.*.unit' => [
                'required_with:products',
                'in:' . implode(',', UnitEnum::values())
            ],
            'products.*.value' => [
                'required_with:products',
                'string'
            ],
            'categories' => [
                'required',
                'array'
            ],
            'categories.*' => [
                'required_with:categories',
                'exists:categories,id'
            ]
        ];
    }
}
