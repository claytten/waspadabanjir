<?php

namespace App\Models\Maps\Fields\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateFieldRequest extends FormRequest
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
            'color'     => ['required', 'string'],
            'description'=>['required', 'string'],
            'deaths'    => ['required', 'numeric', 'min:0'],
            'losts'     => ['required', 'numeric', 'min:0'],
            'injured'   => ['required', 'numeric', 'min:0'],
            'status'    => ['required']
        ];
    }
}
