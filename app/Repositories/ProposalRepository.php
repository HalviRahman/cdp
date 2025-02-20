<?php

namespace App\Repositories;

use App\Models\Proposal;

class ProposalRepository extends Repository
{

    /**
     * constructor method
     *
     * @return void
     */
    public function __construct()
    {
        $this->model = new Proposal();
    }

    public function getFilterTahun()
    {
        $query = $this->model->query();

        $tahun = request('tahun', date('Y'));

        $query->whereYear('tgl_upload', $tahun);

        return $query->latest()->get();
    }

    public function getFilterProdi()
    {
        $user = auth()->user();
        $query = $this->model->query();

        $tahun = request('tahun', date('Y'));

        $query->whereYear('tgl_upload', $tahun);
        $query->where('prodi', json_decode($user->prodi, true));

        return $query->latest()->get();
    }
}
