<?php

namespace App\Models\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            'username'              => ['required', 'string', 'max:191'],
            'name'                  => ['required', 'string'],
            'email'                 => ['required', 'email'],
            'phone'                 => ['required'],
            'address_id'            => ['required'],
            'role'                  => ['required'],
            'position'              => ['required']
        ];
    }
}
