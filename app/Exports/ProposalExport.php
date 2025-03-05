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
        return [['Detail Pendaftaran - SIM-CDP'], ['Program Studi: ' . $this->prodi], ['No', 'Tanggal Upload', 'Judul Proposal', 'Ketua', 'Program Studi', 'Status Prodi']];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge cells untuk judul
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');

        // Dapatkan jumlah baris data
        $dataCount = $this->proposals->count() + 3; // +3 untuk header rows

        // Tambahkan border untuk seluruh data
        $sheet->getStyle('A1:F' . $dataCount)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

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
            'B' => ['width' => 15],
            'C' => ['width' => 50],
            'D' => ['width' => 20],
            'E' => ['width' => 20],
            'F' => ['width' => 15],
        ];
    }

    public function map($proposal): array
    {
        static $no = 0;
        $no++;

        return [$no, $proposal->created_at->format('d M Y'), $proposal->judul_proposal, $proposal->ketuaKelompok->user->name, $proposal->prodi, $this->getStatus($proposal->status)];
    }

    private function getStatus($status)
    {
        switch ($status) {
            case '0':
                return 'Menunggu';
            case '1':
                return 'Disetujui';
            case '10':
                return 'Ditolak';
            default:
                return 'Unknown';
        }
    }
}
