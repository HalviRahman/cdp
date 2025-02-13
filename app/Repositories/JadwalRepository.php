<?php

namespace App\Repositories;

use App\Models\Jadwal;

class JadwalRepository extends Repository
{

    /**
     * constructor method
     *
     * @return void
     */
    public function __construct()
    {
        $this->model = new Jadwal();
    }
}
