<?php

namespace App\Exports;

use Illuminate\Support\Collection; //----- Collection: class bawaan Laravel untuk mengelola data array/objek
use Maatwebsite\Excel\Concerns\FromCollection; //----- FromCollection: interface Laravel Excel — sumber data export adalah Collection
use Maatwebsite\Excel\Concerns\WithHeadings; //----- WithHeadings: interface Laravel Excel — menentukan baris judul (header) di Excel

//----- class PembayaranExport: menyiapkan data pembayaran untuk diekspor ke Excel
class PembayaranExport implements FromCollection, WithHeadings
{
    //----- __construct(): menerima data rows (hasil query pembayaran join servis)
    public function __construct(private iterable $rows)
    {
    }

    //----- collection(): mengubah data pembayaran menjadi baris-baris Excel
    //----- $row->daftar_jasa: string rincian jasa yang sudah disusun di controller
    //----- number_format: format jumlah bayar menjadi Rupiah
    public function collection(): Collection
    {
        $data = [];
        $no = 1;

        foreach ($this->rows as $row) {
            //----- Data kosong ditampilkan sebagai strip
            $noServis = '-';
            if ($row->no_servis != null && $row->no_servis != '') {
                $noServis = $row->no_servis;
            }

            $keluhan = '-';
            if ($row->keluhan != null && $row->keluhan != '') {
                $keluhan = $row->keluhan;
            }

            $data[] = [
                $no, //----- No urut
                $row->no_pembayaran, //----- Nomor pembayaran
                $noServis, //----- No servis
                $keluhan, //----- Keluhan
                $row->daftar_jasa, //----- Rincian jasa yang dibayar
                $row->tanggal, //----- Tanggal pembayaran
                'Rp' . number_format($row->jumlah_bayar, 0, ',', '.'), //----- Jumlah bayar diformat Rupiah
                $row->metode, //----- Metode bayar apa adanya
            ];
            $no++;
        }

        return collect($data);
    }

    //----- headings(): judul kolom yang tampil di baris pertama Excel
    public function headings(): array
    {
        return [
            'No',
            'No Pembayaran',
            'No Servis',
            'Keluhan',
            'Jasa Dipilih',
            'Tanggal',
            'Jumlah Bayar',
            'Metode',
        ];
    }
}
