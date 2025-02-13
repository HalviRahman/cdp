<?php

namespace App\Repositories;

use App\Models\ProgramStudi;

class ProgramStudiRepository extends Repository
{

    /**
     * constructor method
     *
     * @return void
     */
    public function __construct()
    {
        $this->model = new ProgramStudi();
    }
}
