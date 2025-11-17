<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PeminjamanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $peminjaman;

    public function __construct($peminjaman)
    {
        $this->peminjaman = $peminjaman;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->peminjaman;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No. Transaksi',
            'Peminjam',
            'Email',
            'Status',
            'Tanggal Pinjam',
            'Tanggal Estimasi Kembali',
            'Tanggal Kembali',
            'Jumlah Barang',
            'Denda (Rp)',
            'Disetujui Oleh',
            'Catatan',
        ];
    }

    /**
     * @var mixed $peminjaman
     */
    public function map($peminjaman): array
    {
        return [
            $peminjaman->no_transaksi,
            $peminjaman->user->name,
            $peminjaman->user->email,
            strtoupper($peminjaman->status),
            $peminjaman->tgl_pinjam->format('d-m-Y'),
            $peminjaman->tgl_estimasi_kembali->format('d-m-Y'),
            $peminjaman->tgl_kembali ? $peminjaman->tgl_kembali->format('d-m-Y') : '-',
            $peminjaman->items->count(),
            $peminjaman->total_denda,
            $peminjaman->approver ? $peminjaman->approver->name : '-',
            $peminjaman->catatan ?? '-',
        ];
    }

    /**
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Laporan Peminjaman';
    }
}