<?php

namespace App\Exports;

use Illuminate\Support\Collection; //----- Collection: class bawaan Laravel untuk mengelola data array/objek
use Maatwebsite\Excel\Concerns\FromCollection; //----- FromCollection: interface Laravel Excel — sumber data export adalah Collection
use Maatwebsite\Excel\Concerns\WithHeadings; //----- WithHeadings: interface Laravel Excel — menentukan baris judul (header) di Excel

//----- class KendaraanExport: menyiapkan data kendaraan untuk diekspor ke Excel
class KendaraanExport implements FromCollection, WithHeadings
{
    //----- __construct(): menerima data rows (hasil query database) saat class dipanggil dari controller
    public function __construct(private iterable $rows)
    {
    }

    //----- collection(): mengubah data database menjadi baris-baris Excel
    //----- $row->nama_pelanggan bisa kosong, fallback ke pelanggan_id
    public function collection(): Collection
    {
        $data = [];
        $no = 1;

        foreach ($this->rows as $row) {
            $namaPelanggan = $row->pelanggan_id;
            if ($row->nama_pelanggan != null && $row->nama_pelanggan != '') {
                $namaPelanggan = $row->nama_pelanggan;
            }

            $data[] = [
                $no, //----- No urut
                $row->no_polisi, //----- Nomor polisi kendaraan
                $namaPelanggan, //----- Nama pelanggan pemilik kendaraan
                $row->merk, //----- Merk kendaraan
                $row->model, //----- Model kendaraan
                $row->tahun, //----- Tahun pembuatan
                $row->warna, //----- Warna kendaraan
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
            'No Polisi',
            'Pelanggan',
            'Merk',
            'Model',
            'Tahun',
            'Warna',
        ];
    }
}
