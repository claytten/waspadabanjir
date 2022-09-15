<?php

namespace App\Models\Reports\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

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
        $rule = [
            'name'        => ['required', 'string'],
            'report_type' => ['required', Rule::in(['ask', 'report', 'suggest'])],
            'message'     => ['required', 'string'],
        ];

        if(request('report_type') == 'report') {
            $rule['phone'] = ['required', 'string', 'max:15'];
            $rule['address'] = ['required', 'string'];
            $rule['images'] = ['required', 'array'];
        }
        return $rule;
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
