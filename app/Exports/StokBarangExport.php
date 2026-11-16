<?php

namespace App\Exports;

use Illuminate\Support\Collection; //----- Collection: class bawaan Laravel untuk mengelola data array/objek
use Maatwebsite\Excel\Concerns\FromCollection; //----- FromCollection: interface Laravel Excel — sumber data export adalah Collection
use Maatwebsite\Excel\Concerns\WithHeadings; //----- WithHeadings: interface Laravel Excel — menentukan baris judul (header) di Excel

//----- class StokBarangExport: menyiapkan rekap stok barang untuk diekspor ke Excel
class StokBarangExport implements FromCollection, WithHeadings
{
    //----- __construct(): menerima data rows (hasil query rekap stok_masuk/keluar per barang)
    public function __construct(private iterable $rows)
    {
    }

    //----- collection(): mengubah data stok menjadi baris-baris Excel
    //----- stok_masuk/tanggal_masuk & stok_keluar/tanggal_keluar: hasil agregasi SUM/MAX dari query SQL di controller
    //----- number_format: format harga & harga jual menjadi Rupiah
    public function collection(): Collection
    {
        $data = [];
        $no = 1;

        foreach ($this->rows as $row) {
            //----- Nama barang kosong -> strip
            $namaBarang = '-';
            if ($row->nama_barang != null && $row->nama_barang != '') {
                $namaBarang = $row->nama_barang;
            }

            //----- Harga jual kosong -> anggap 0
            $hargaJual = 0;
            if ($row->harga_jual != null) {
                $hargaJual = $row->harga_jual;
            }

            $data[] = [
                $no, //----- No urut
                $row->nama_supplier, //----- Nama supplier
                $namaBarang, //----- Nama barang
                'Rp' . number_format($row->harga, 0, ',', '.'), //----- Harga modal diformat Rupiah
                $row->stok, //----- Stok saat ini
                $row->stok_masuk, //----- Total stok masuk
                $row->tanggal_masuk, //----- Tanggal masuk terakhir
                $row->stok_keluar, //----- Total stok keluar
                $row->tanggal_keluar, //----- Tanggal keluar terakhir
                'Rp' . number_format($hargaJual, 0, ',', '.'), //----- Harga jual diformat Rupiah
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
            'Supplier',
            'Nama Barang',
            'Harga',
            'Stok Sekarang',
            'Stok Masuk',
            'Tanggal Masuk',
            'Stok Keluar',
            'Tanggal Keluar',
            'Harga Jual',
        ];
    }
}
