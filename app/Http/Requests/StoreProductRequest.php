<?php

namespace App\Http\Requests;

use App\Models\Product;
use App\Rules\TranslatableUnique;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
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
            'name' => [
                'required',
                new TranslatableUnique('products', 'name'),
                'string'
            ],
            'description' => [
                'nullable',
                'string'
            ],
            'image' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,heic',
                'max:20000'
            ],
            'category_id' => [
                'required',
                'exists:product_categories,id'
            ]
        ];
    }
}
