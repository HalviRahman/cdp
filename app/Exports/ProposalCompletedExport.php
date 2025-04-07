<?php

namespace App\Exports;

use App\Models\Proposal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use App\Models\User;

class ProposalCompletedExport implements FromCollection, WithMapping, WithCustomCsvSettings
{
    protected $tahun;

    public function __construct($tahun = null)
    {
        $this->tahun = $tahun ?? date('Y');
    }

    public function collection()
    {
        return Proposal::with(['kelompoks.user', 'ketuaKelompok.user'])
            ->where('status', 2)
            // ->whereNotNull('laporan_kegiatan')
            // ->whereNotNull('laporan_perjalanan')
            ->whereYear('created_at', $this->tahun)
            ->get();
    }

    // public function headings(): array
    // {
    //     return ['NIP/NIPPPK/NIM', 'NAMA PELAKSANA', 'PROGRAM STUDI', 'JUDUL'];
    // }

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
                // 'prodi' => $proposal->ketuaKelompok->user->prodi,
            ]);
        }

        // Tambahkan anggota dosen
        $proposal->kelompoks->where('peran', 'Anggota')->each(function ($anggota) use ($allMembers) {
            if ($anggota->user) {
                $allMembers->push([
                    'name' => $anggota->user->name,
                    'nip' => $anggota->user->nip,
                    // 'prodi' => $anggota->user->prodi,
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
            $rows[] = [$member['nip'], $member['name'], $proposal->prodi, $proposal->judul_proposal];
        }

        return $rows;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';',
        ];
    }
}
