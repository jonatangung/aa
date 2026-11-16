<?php

namespace App\Exports;

use Illuminate\Support\Collection; //----- Collection: class bawaan Laravel untuk mengelola data array/objek
use Maatwebsite\Excel\Concerns\FromCollection; //----- FromCollection: interface Laravel Excel — sumber data export adalah Collection
use Maatwebsite\Excel\Concerns\WithHeadings; //----- WithHeadings: interface Laravel Excel — menentukan baris judul (header) di Excel

//----- class LaporanServisExport: menyiapkan data laporan servis untuk diekspor ke Excel
class LaporanServisExport implements FromCollection, WithHeadings
{
    //----- __construct(): menerima data rows (hasil query join servis+pelanggan+kendaraan+mekanik)
    public function __construct(private Collection $rows)
    {
    }

    //----- collection(): mengubah data laporan menjadi baris-baris Excel
    //----- Data kosong ditampilkan sebagai strip
    public function collection(): Collection
    {
        $data = [];
        $no = 1;

        foreach ($this->rows as $row) {
            $namaMekanik = '-';
            if ($row->nama_mekanik != null && $row->nama_mekanik != '') {
                $namaMekanik = $row->nama_mekanik;
            }

            $keluhan = '-';
            if ($row->keluhan != null && $row->keluhan != '') {
                $keluhan = $row->keluhan;
            }

            $data[] = [
                $no, //----- No urut
                $row->no_servis, //----- Nomor servis
                $row->nama_pelanggan, //----- Nama pelanggan
                $row->no_polisi, //----- No polisi kendaraan
                $namaMekanik, //----- Nama mekanik
                $row->tanggal_masuk, //----- Tanggal servis masuk
                $keluhan, //----- Keluhan pelanggan
                $row->status, //----- Status apa adanya
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
            'No Servis',
            'Pelanggan',
            'No Polisi',
            'Mekanik',
            'Tanggal Masuk',
            'Keluhan',
            'Status',
        ];
    }
}
