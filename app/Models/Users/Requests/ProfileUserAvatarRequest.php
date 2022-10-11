<?php

namespace App\Models\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUserAvatarRequest extends FormRequest
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
      'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
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
      'image.required' => 'Berkas foto tidak boleh kosong!',
      'image.image' => 'File harus berupa gambar',
      'image.mimes' => 'File harus berupa gambar dengan format jpeg, png, atau jpg',
      'image.max' => 'Ukuran file maksimal 2 MB',
    ];
  }
}
