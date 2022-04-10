<?php

namespace App\Models\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
            'name'      => ['required'],
            'email'     => ['required', 'email', 'unique:admin'],
            'password'  => ['required', 'string', 'min:5', 'confirmed'],
            'phone'     => ['required'],
            'address'   => ['required', 'string'],
            'role'      => ['required']
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
            'name.required'     => 'Nama harus diisi',
            'email.required'    => 'Email harus diisi',
            'email.email'       => 'Email harus berupa email',
            'email.unique'      => 'Email sudah ada',
            'password.required' => 'Password harus diisi',
            'password.string'   => 'Password harus berupa string',
            'password.min'      => 'Password minimal 5 karakter',
            'password.confirmed'=> 'Password tidak sama',
            'phone.required'    => 'Nomor telepon harus diisi',
            'address.required'  => 'Alamat harus diisi',
            'address.string'    => 'Alamat harus berupa string',
            'role.required'     => 'Role harus diisi'
        ];
    }
}
