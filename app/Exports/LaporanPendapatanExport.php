<?php

namespace App\Exports;

use Illuminate\Support\Collection; //----- Collection: class bawaan Laravel untuk mengelola data array/objek
use Maatwebsite\Excel\Concerns\FromCollection; //----- FromCollection: interface Laravel Excel — sumber data export adalah Collection
use Maatwebsite\Excel\Concerns\WithHeadings; //----- WithHeadings: interface Laravel Excel — menentukan baris judul (header) di Excel

//----- class LaporanPendapatanExport: menyiapkan data laporan pendapatan untuk diekspor ke Excel
class LaporanPendapatanExport implements FromCollection, WithHeadings
{
    //----- __construct(): menerima 2 data:
    //----- $rows = daftar pembayaran (sudah ada properti daftar_jasa & total_jasa dari controller)
    //----- $totalPendapatan = total keseluruhan
    public function __construct(
        private Collection $rows,
        private float $totalPendapatan
    ) {
    }

    //----- collection(): mengubah data pembayaran menjadi baris-baris Excel
    //----- Baris terakhir berisi TOTAL PENDAPATAN keseluruhan
    public function collection(): Collection
    {
        $data = [];
        $no = 1;

        foreach ($this->rows as $row) {
            $noServis = '-';
            if ($row->no_servis != null && $row->no_servis != '') {
                $noServis = $row->no_servis;
            }

            $data[] = [
                $no, //----- No urut
                $row->no_pembayaran, //----- Nomor pembayaran
                $noServis, //----- No servis
                $row->daftar_jasa, //----- Rincian jasa yang dibayar
                $row->tanggal, //----- Tanggal pembayaran
                'Rp' . number_format($row->total_jasa, 0, ',', '.'), //----- Total bayar diformat Rupiah
                $row->metode, //----- Metode bayar apa adanya
            ];
            $no++;
        }

        //----- Tambah baris TOTAL di paling bawah tabel
        $data[] = [
            '',
            '',
            '',
            '',
            'Total Pendapatan',
            'Rp' . number_format($this->totalPendapatan, 0, ',', '.'),
            '',
        ];

        return collect($data);
    }

    //----- headings(): judul kolom yang tampil di baris pertama Excel
    public function headings(): array
    {
        return [
            'No',
            'No Pembayaran',
            'No Servis',
            'Jasa Dipilih',
            'Tanggal',
            'Jumlah Bayar',
            'Metode',
        ];
    }
}
