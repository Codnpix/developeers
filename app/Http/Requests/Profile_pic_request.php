<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Profile_pic_request extends FormRequest
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
            'file'=>'bail|required|image|dimensions:min_width=50,min_height=50'
        ];
    }
}
