<?php

namespace App\Models\Reports\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateReportRequest extends FormRequest
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
            'name'      => ['required', 'string'],
            'address'   => ['required'],
            'phone'     => ['required']
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
            'name.string'       => 'Nama harus berupa string',
            'address.required'  => 'Alamat harus diisi',
            'phone.required'    => 'Nomor telepon harus diisi'
        ];
    }
}
