<?php

namespace App\Repositories;

use App\Models\Kelompok;

class KelompokRepository extends Repository
{

    /**
     * constructor method
     *
     * @return void
     */
    public function __construct()
    {
        $this->model = new Kelompok();
    }
}
