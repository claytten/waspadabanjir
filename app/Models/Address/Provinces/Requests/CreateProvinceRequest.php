<?php

namespace App\Models\Address\Provinces\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class CreateProvinceRequest extends FormRequest
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
            'name'              => ['required', 'string', 'max:191', 'unique:provinces,name']
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
            'name.max'          => 'Nama maksimal 191 karakter',
            'name.unique'       => 'Nama sudah ada'
        ];
    }

    /**
    * Get the error messages for the defined validation rules.*
    * @return array
    */
    protected function failedValidation(Validator $validator)
    {
        if ($validator->errors()->has('name')) {
            throw new HttpResponseException(response()->json([
                'status'    => 'error',
                'message'   => 'Data Provinsi Sudah Ada!'
            ], 200));
        }
    }
}
