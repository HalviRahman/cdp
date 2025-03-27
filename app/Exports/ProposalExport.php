<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;
use App\Models\User;

class ProposalExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize
{
    protected $proposals;
    protected $prodi;

    public function __construct($proposals, $prodi)
    {
        $this->proposals = $proposals;
        $this->prodi = $prodi;
    }

    public function collection()
    {
        return $this->proposals;
    }

    public function title(): string
    {
        return 'Detail Pendaftaran - SIM-CDP';
    }

    public function headings(): array
    {
        return [['Detail Pendaftaran - SIM-CDP'], ['Program Studi: ' . $this->prodi], ['No', 'Judul Proposal', 'Ketua', 'Anggota', 'Program Studi', 'Status Prodi', 'File Proposal', 'Laporan Kegiatan', 'Laporan Perjalanan']];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge cells untuk judul
        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');

        // Dapatkan jumlah baris data
        $dataCount = $this->proposals->count() + 3; // +3 untuk header rows

        // Tambahkan border untuk seluruh data
        $sheet->getStyle('A1:I' . $dataCount)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Style untuk hyperlink
        $hyperlinkStyle = [
            'font' => [
                'color' => ['rgb' => '0000FF'], // Warna biru
                'underline' => Font::UNDERLINE_SINGLE,
            ],
        ];

        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => '4B5366'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            2 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => '4B5366'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            3 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => '4B5366'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            'A' => ['width' => 5],
            'B' => ['width' => 50],
            'C' => ['width' => 50],
            'D' => ['width' => 20],
            'E' => ['width' => 20],
            'F' => ['width' => 15],
            'G' => ['width' => 15],
            'H' => ['width' => 20],
            'I' => ['width' => 20],
        ];
    }

    public function map($proposal): array
    {
        static $no = 0;
        $no++;

        // Ambil nama-nama anggota dosen
        $anggotaNames = $proposal->kelompoks->where('peran', 'Anggota')->pluck('user.name')->filter();

        // Ambil mahasiswa dengan NIP dan nama
        $mahasiswaNames = User::where('remember_token', $proposal->id_kelompok)
            ->where('is_mahasiswa', 1)
            ->get()
            ->map(function ($mhs) {
                return $mhs->nip . ' - ' . $mhs->name;
            });

        // Gabungkan anggota dosen dan mahasiswa
        $allAnggota = $anggotaNames->concat($mahasiswaNames)->implode(', ');

        return [$no, $proposal->judul_proposal, $proposal->ketuaKelompok->user->name, $allAnggota ?: '-', $proposal->prodi, $this->getStatus($proposal->status), $proposal->file_proposal ? '=HYPERLINK("' . $proposal->file_proposal . '", "Lihat")' : '-', $proposal->laporan_kegiatan ? '=HYPERLINK("' . $proposal->laporan_kegiatan . '", "Lihat")' : '-', $proposal->laporan_perjalanan ? '=HYPERLINK("' . $proposal->laporan_perjalanan . '", "Lihat")' : '-'];
    }

    private function getStatus($status)
    {
        switch ($status) {
            case '0':
                return 'Menunggu Verifikasi Koordinator Prodi';
            case '1':
                return 'Menunggu Verifikasi Prodi';
            case '2':
                return 'Disetujui';
            case '3':
                return 'Disetujui';
            case '10':
                return 'Ditolak';
            default:
                return 'Unknown';
        }
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'G' => 'HYPERLINK',
            'H' => 'HYPERLINK',
            'I' => 'HYPERLINK',
        ];
    }
}
