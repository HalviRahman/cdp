<?php

namespace App\Exports;

use App\Models\Proposal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\User;

class ProposalCompletedExport implements FromCollection, WithHeadings, WithMapping
{
    protected $tahun;

    public function __construct($tahun = null)
    {
        $this->tahun = $tahun ?? date('Y');
    }

    public function collection()
    {
        return Proposal::with(['kelompoks.user', 'ketuaKelompok.user'])
            ->where('status', 3)
            // ->whereNotNull('laporan_kegiatan')
            // ->whereNotNull('laporan_perjalanan')
            ->whereYear('created_at', $this->tahun)
            ->get();
    }

    public function headings(): array
    {
        return ['NAMA PELAKSANA', 'NIP/NIPPPK/NIM', 'JUDUL', 'PROGRAM STUDI'];
    }

    public function map($proposal): array
    {
        static $no = 0;
        $no++;

        // Ambil semua anggota (dosen dan mahasiswa)
        $allMembers = collect();

        // Tambahkan ketua kelompok
        if ($proposal->ketuaKelompok && $proposal->ketuaKelompok->user) {
            $allMembers->push([
                'name' => $proposal->ketuaKelompok->user->name,
                'nip' => $proposal->ketuaKelompok->user->nip,
            ]);
        }

        // Tambahkan anggota dosen
        $proposal->kelompoks->where('peran', 'Anggota')->each(function ($anggota) use ($allMembers) {
            if ($anggota->user) {
                $allMembers->push([
                    'name' => $anggota->user->name,
                    'nip' => $anggota->user->nip,
                ]);
            }
        });

        // Tambahkan mahasiswa
        User::where('remember_token', $proposal->id_kelompok)
            ->where('is_mahasiswa', 1)
            ->get()
            ->each(function ($mhs) use ($allMembers) {
                $allMembers->push([
                    'name' => $mhs->name,
                    'nip' => $mhs->nip,
                    'prodi' => $mhs->prodi,
                ]);
            });

        // Buat array untuk menyimpan baris-baris data
        $rows = [];

        // Setiap anggota mendapatkan nomor dan judul yang sama
        foreach ($allMembers as $member) {
            $rows[] = [$member['name'], $member['nip'], $proposal->judul_proposal, $proposal->prodi];
        }

        return $rows;
    }
}
