<?php

namespace App\Http\Requests;

use App\Enums\UnitEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class AddProductToShoppingListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'unit' => [
                'required_with:value',
                new Enum(UnitEnum::class)
            ],
            'value' => [
                'required_with:unit',
                'string'
            ],
            'custom_name' => [
                'required_without:product_id',
                'string'
            ],
            'product_id' => [
                'required_without:custom_name',
                'exists:products,id'
            ],
        ];
    }
}
