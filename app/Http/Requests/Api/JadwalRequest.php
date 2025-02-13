<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class JadwalRequest extends FormRequest
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
        if ($this->isMethod('put')) {
            return [
				'tgl_mulai' => ["required"],
				'tgl_selesai' => ["required"],
				'keterangan' => ["required"],

            ];
        }
        return [
			'tgl_mulai' => ["required"],
			'tgl_selesai' => ["required"],
			'keterangan' => ["required"],

        ];
    }
}
