<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserPreferenceRequest extends FormRequest
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
    public function rules()
    {
        return [
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'min_calories' => ['nullable', 'integer', 'min:0'],
            'max_calories' => ['nullable', 'numeric', 'min:0'],
            'min_difficulty_of_execution' => ['nullable', 'integer', 'min:0'],
            'max_difficulty_of_execution' => ['nullable', 'integer', 'min:0'],
            'min_execution_time' => ['nullable', 'integer', 'min:0'],
            'max_execution_time' => ['nullable', 'integer', 'min:0']
        ];
    }
}
