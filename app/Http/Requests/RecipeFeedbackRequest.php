<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecipeFeedbackRequest extends FormRequest
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
            'feedback' => [
                'required_without:feedback_star',
                'string',
                'min:1'
            ],
            'feedback_star' => [
                'required_without:feedback',
                'integer',
                'min:1',
                'max:10'
            ],
            'images' => [
                'array',
                'nullable'
            ],
            'images.*' => [
                'mimes:jpg,png,bmp,jpeg',
                'image'
            ]
        ];
    }
}
