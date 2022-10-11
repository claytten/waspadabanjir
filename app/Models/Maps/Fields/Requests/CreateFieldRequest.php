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
        $rule = [
            'description'=>['required', 'string'],
            'deaths'    => ['required', 'numeric', 'min:0'],
            'losts'     => ['required', 'numeric', 'min:0'],
            'injured'   => ['required', 'numeric', 'min:0'],
            'date_in'   => ['required', 'date'],
            'locations' => ['required', 'array'],
            'status'    => ['required'],
            'level'     => ['required']
        ];
        if(request()->hasFile('images')) {
            $rule['images.*'] = ['required', 'mimes:jpeg,png,jpg', 'max:5000'];
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
        $message = [
            'description.required'  => 'Deskripsi harus diisi',
            'description.string'    => 'Deskripsi harus berupa string',
            'level.required'        => 'Level harus diisi',
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
        if(request()->hasFile('images')) {
            $message['images.*.required'] = 'Berkas foto tidak boleh kosong!';
            $message['images.*.mimes'] = 'File harus berupa gambar dengan format jpeg, png, atau jpg';
            $message['images.*.max'] = 'Ukuran file maksimal untuk semua berkas 5 MB';
        }
        return $message;
    }
}
