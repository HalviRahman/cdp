<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class LaporanExport implements FromView
{
    use Exportable;

    /**
     * data
     *
     * @var Collection
     */
    private Collection $data;

    /**
     * constructor method
     *
     * @param Collection $data
     * @return void
     */
    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    /**
     * export from view
     *
     * @return View
     */
    public function view(): View
    {
        if ($this->data->count() === 0) {
            $columns = [
				'id_kelompok',
				'judul_proposal',
				'file_proposal',
				'tgl_upload',
				'status',
				'verifikator',
				'keterangan',
				'tgl_verifikasi',
            ];
            $data = [];

            foreach (range(1, 10) as $i) {
                array_push($data, (object) array_combine($columns, $columns));
            }

            $this->data = collect($data);
        }
        return view('stisla.laporans.export-excel-example', [
            'data'     => $this->data,
            'isExport' => true
        ]);
    }
}
