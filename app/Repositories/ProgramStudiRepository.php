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

    public function getFilterTahun()
    {
        $query = $this->model->query();

        $tahun = request('tahun', date('Y'));

        $query->where('tahun', $tahun);

        return $query->latest()->get();
    }
}
