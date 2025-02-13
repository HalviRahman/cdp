<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LaporanRequest extends FormRequest
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
				'id_kelompok' => ["required"],
				'judul_proposal' => ["required"],
				'file_proposal' => ["required"],
				'tgl_upload' => [],
				'status' => [],
				'verifikator' => ["required"],
				'keterangan' => [],
				'tgl_verifikasi' => [],

            ];
        }
        return [
			'id_kelompok' => ["required"],
			'judul_proposal' => ["required"],
			'file_proposal' => ["required"],
			'tgl_upload' => [],
			'status' => [],
			'verifikator' => ["required"],
			'keterangan' => [],
			'tgl_verifikasi' => [],

        ];
    }
}
