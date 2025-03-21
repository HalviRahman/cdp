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
        if ($user->hasRole('Prodi')) {
            $query->where(function ($q) use ($user) {
                $q->where('prodi', $user->kaprodi);
                // $q->orWhere('prodi', $user->prodi);
            });
        }
        if ($user->hasRole('Koordinator Prodi')) {
            $query->where(function ($q) use ($user) {
                $q->where('prodi', $user->prodi);
            });
        }
        // $query->where('prodi', $user->prodi);
        // $query->orWhere('prodi', $user->kaprodi);

        return $query->latest()->get();
    }

    public function getFilterProdiCount()
    {
        $user = auth()->user();
        $query = $this->model->query();
        $tahun = request('tahun', date('Y'));

        $query->whereYear('tgl_upload', $tahun);
        // $query->where('prodi', $user->prodi);
        // $query->orWhere('prodi', $user->kaprodi);

        return $query->count();
    }
}
