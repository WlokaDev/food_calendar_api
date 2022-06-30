<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecipeSearchRequest extends FormRequest
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
    public function rules() : array
    {
        return [
            'q' => ['nullable', 'string'],
            'min_price' => ['nullable', 'number', 'min:0'],
            'max_price' => ['nullable', 'number', 'min:0'],
            'min_calories' => ['nullable', 'integer', 'min:0'],
            'max_calories' => ['nullable', 'integer', 'min:0'],
            'difficulty_of_execution' => ['nullable', 'integer', 'min:1', 'max:10'],
            'max_execution_time' => ['nullable', 'integer', 'min:1'],
            'page' => ['nullable', 'integer'],
            'per_page' => ['nullable', 'integer']
        ];
    }
}
