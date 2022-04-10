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
            'name'                  => ['required', 'string'],
            'email'                 => ['required', 'email'],
            'phone'                 => ['required'],
            'address'               => ['required', 'string'],
            'role'                  => ['required'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     * 
     * @return array
     */
    public function messages()
    {
        return [
            'name.required'         => 'Nama harus diisi',
            'name.string'           => 'Nama harus berupa string',
            'email.required'        => 'Email harus diisi',
            'email.email'           => 'Email harus berupa email',
            'phone.required'        => 'Nomor telepon harus diisi',
            'address.required'      => 'Alamat harus diisi',
            'address.string'        => 'Alamat harus berupa string',
            'role.required'         => 'Role harus diisi',
        ];
    }
}
