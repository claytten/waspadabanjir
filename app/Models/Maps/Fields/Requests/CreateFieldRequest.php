<?php

namespace App\Models\Maps\Fields\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateFieldRequest extends FormRequest
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
            'color'     => ['required', 'string'],
            'description'=>['required', 'string'],
            'deaths'    => ['required', 'numeric', 'min:0'],
            'losts'     => ['required', 'numeric', 'min:0'],
            'injured'   => ['required', 'numeric', 'min:0'],
            'date_in'   => ['required', 'date'],
            'locations' => ['required', 'array'],
            'status'    => ['required']
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
            'description.required'  => 'Deskripsi harus diisi',
            'description.string'    => 'Deskripsi harus berupa string',
            'color.required'        => 'Warna harus diisi',
            'color.string'          => 'Warna harus berupa string',
            'deaths.required'       => 'Jumlah kematian harus diisi',
            'deaths.numeric'        => 'Jumlah kematian harus berupa angka',
            'deaths.min'            => 'Jumlah kematian minimal 0',
            'losts.required'        => 'Jumlah hilang harus diisi',
            'losts.numeric'         => 'Jumlah hilang harus berupa angka',
            'losts.min'             => 'Jumlah hilang minimal 0',
            'injured.required'      => 'Jumlah pengungsi harus diisi',
            'injured.numeric'       => 'Jumlah pengungsi harus berupa angka',
            'injured.min'           => 'Jumlah pengungsi minimal 0',
            'date_in.required'      => 'Tanggal harus diisi',
            'date_in.date'          => 'Tanggal harus berupa tanggal',
            'locations.required'    => 'Lokasi harus diisi',
            'locations.array'       => 'Lokasi harus berupa array',
            'status.required'       => 'Status harus diisi'
        ];
    }
}
