<?php

namespace App\Repositories;

use App\Models\Laporan;

class LaporanRepository extends Repository
{

    /**
     * constructor method
     *
     * @return void
     */
    public function __construct()
    {
        $this->model = new Laporan();
    }
}
