<?php

namespace App\Models\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUserRequest extends FormRequest
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
      'name' => ['required', 'string', 'max:191'],
      'email' => ['required', 'email', 'max:191', 'unique:admin,email,' . $this->userId],
      'phone' => ['required', 'string', 'max:191']
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
      'name.required' => 'Nama tidak boleh kosong',
      'name.string' => 'Nama harus berupa string',
      'name.max' => 'Nama maksimal 191 karakter',
      'email.required' => 'Email tidak boleh kosong',
      'email.email' => 'Email tidak valid',
      'email.max' => 'Email maksimal 191 karakter',
      'email.unique' => 'Email sudah terdaftar',
      'phone.required' => 'Nomor telepon tidak boleh kosong',
      'phone.string' => 'Nomor telepon harus berupa string',
      'phone.max' => 'Nomor telepon maksimal 191 karakter',
    ];
  }
}
