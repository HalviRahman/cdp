<?php

namespace App\Repositories;

use App\Models\Proposal;
use Illuminate\Support\Facades\Storage;

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

    public function findByToken($token)
    {
        return $this->model->where('token', $token)->first();
    }

    public function deleteByToken($token)
    {
        $proposal = $this->findByToken($token);
        if ($proposal) {
            // Hapus file proposal jika ada
            if ($proposal->file_proposal) {
                Storage::delete($proposal->file_proposal);
            }

            // Hapus file laporan jika ada
            if ($proposal->laporan_kegiatan) {
                Storage::delete($proposal->laporan_kegiatan);
            }
            if ($proposal->laporan_perjalanan) {
                Storage::delete($proposal->laporan_perjalanan);
            }

            // Hapus data dari database
            return $proposal->delete();
        }
        return false;
    }
}
