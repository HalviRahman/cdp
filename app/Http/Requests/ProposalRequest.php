<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProposalRequest extends FormRequest
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
                'id_kelompok' => [],
                'judul_proposal' => ['required'],
                'file_proposal' => ['file', 'mimes:pdf', 'max:10240'],
                'tgl_upload' => [],
                'status' => [],
                'verifikator' => [],
                'keterangan' => [],
                'tgl_verifikasi' => [],
                'mahasiswa' => [],
                'token' => [],
            ];
        }
        return [
            'id_kelompok' => [],
            'judul_proposal' => ['required'],
            'file_proposal' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'tgl_upload' => [],
            'status' => [],
            'verifikator' => [],
            'keterangan' => [],
            'tgl_verifikasi' => [],
            'mahasiswa' => [],
            'token' => [],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'file_proposal.max' => 'Ukuran file proposal tidak boleh lebih dari 10MB',
            'file_proposal.mimes' => 'File proposal harus berformat PDF',
            'file_proposal.required' => 'File proposal wajib diupload',
        ];
    }
}
