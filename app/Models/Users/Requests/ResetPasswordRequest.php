<?php

namespace App\Models\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResetPasswordRequest extends FormRequest
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
            'oldpassword'           => ['required'],
            'password'              => ['required', 'string', 'min:5', 'confirmed'],
            'password_confirmation' => ['required']
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
            'oldpassword.required'           => 'Password lama harus diisi',
            'password.required'              => 'Password baru harus diisi',
            'password.string'                => 'Password baru harus berupa string',
            'password.min'                   => 'Password baru minimal 5 karakter',
            'password.confirmed'             => 'Password baru dan konfirmasi password baru harus sama',
            'password_confirmation.required' => 'Konfirmasi password baru harus diisi'
        ];
    }
}
