<?php

namespace App\Models\Address\Regencies\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRegencyRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:191', 'unique:regencies,name']
        ];
    }
}
