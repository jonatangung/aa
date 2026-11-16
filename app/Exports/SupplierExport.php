<?php

namespace App\Exports;

use Illuminate\Support\Collection; //----- Collection: class bawaan Laravel untuk mengelola data array/objek
use Maatwebsite\Excel\Concerns\FromCollection; //----- FromCollection: interface Laravel Excel — sumber data export adalah Collection
use Maatwebsite\Excel\Concerns\WithHeadings; //----- WithHeadings: interface Laravel Excel — menentukan baris judul (header) di Excel

//----- class SupplierExport: menyiapkan data supplier/barang untuk diekspor ke file Excel
//----- implements FromCollection & WithHeadings: wajib diimplementasikan agar Laravel Excel tahu cara mengambil data & judul kolom
class SupplierExport implements FromCollection, WithHeadings
{
    //----- __construct(): constructor — menerima data rows (hasil query database) saat class ini dipanggil dari controller
    //----- private iterable $rows: property privat yang menyimpan data baris; hanya bisa diakses di dalam class ini
    public function __construct(private iterable $rows)
    {
    }

    //----- collection(): method wajib dari FromCollection — mengubah data database menjadi format baris Excel
    //----- number_format(..., 0, ',', '.'): memformat angka harga menjadi Rupiah (misal: Rp1.500.000)
    //----- Data kosong/null ditampilkan sebagai strip agar tidak kosong di Excel
    public function collection(): Collection
    {
        $data = [];
        $no = 1;

        foreach ($this->rows as $row) {
            $namaBarang = '-';
            if ($row->nama_barang != null && $row->nama_barang != '') {
                $namaBarang = $row->nama_barang;
            }

            $tanggalMasuk = '-';
            if ($row->tanggal_masuk != null && $row->tanggal_masuk != '') {
                $tanggalMasuk = $row->tanggal_masuk;
            }

            $data[] = [
                $no, //----- No urut
                $row->nama_supplier, //----- Nama Supplier / PT
                $namaBarang, //----- Nama Barang
                $row->stok, //----- Stok saat ini
                'Rp' . number_format($row->harga, 0, ',', '.'), //----- Harga diformat Rupiah
                $tanggalMasuk, //----- Tanggal Masuk
                $row->no_hp, //----- No HP supplier
            ];
            $no++;
        }

        return collect($data);
    }

    //----- headings(): method wajib dari WithHeadings — mengembalikan array judul kolom yang tampil di baris pertama Excel
    public function headings(): array
    {
        return [
            'No',
            'Nama Supplier / PT',
            'Nama Barang',
            'Stok',
            'Harga',
            'Tanggal Masuk',
            'No HP',
        ];
    }
}
