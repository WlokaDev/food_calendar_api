<?php

namespace App\Http\Requests;

use App\Rules\TranslatableUnique;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => [
                'required',
                new TranslatableUnique('products', 'name', request()->product->id),
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
