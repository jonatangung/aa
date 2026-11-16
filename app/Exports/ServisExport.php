<?php

namespace App\Exports;

use Illuminate\Support\Collection; //----- Collection: class bawaan Laravel untuk mengelola data array/objek
use Maatwebsite\Excel\Concerns\FromCollection; //----- FromCollection: interface Laravel Excel — sumber data export adalah Collection
use Maatwebsite\Excel\Concerns\WithHeadings; //----- WithHeadings: interface Laravel Excel — menentukan baris judul (header) di Excel

//----- class ServisExport: menyiapkan data transaksi servis untuk diekspor ke Excel
class ServisExport implements FromCollection, WithHeadings
{
    //----- __construct(): menerima data rows (hasil query servis join pelanggan/kendaraan/mekanik)
    public function __construct(private iterable $rows)
    {
    }

    //----- collection(): mengubah data servis menjadi baris-baris Excel
    //----- $row->daftar_jasa, $row->total_jasa, $row->metode_pembayaran: properti tambahan yang disusun di controller (servisData)
    //----- number_format: format total harga menjadi Rupiah
    public function collection(): Collection
    {
        $data = [];
        $no = 1;

        //----- Ulangi tiap baris data servis
        foreach ($this->rows as $row) {
            //----- Nama mekanik kosong -> tampilkan strip
            $namaMekanik = '-';
            if ($row->nama_mekanik != '') {
                $namaMekanik = $row->nama_mekanik;
            }

            $data[] = [
                $no, //----- No urut
                $row->no_servis, //----- Nomor servis
                $row->nama_pelanggan, //----- Nama pelanggan
                $row->no_polisi, //----- No polisi kendaraan
                $row->daftar_jasa, //----- Rincian jasa (string dari controller)
                'Rp' . number_format($row->total_jasa, 0, ',', '.'), //----- Total harga diformat Rupiah
                $namaMekanik, //----- Nama mekanik
                $row->metode_pembayaran, //----- Metode pembayaran
                $row->tanggal_masuk, //----- Tanggal masuk servis
                $row->status, //----- Status apa adanya
            ];
            $no++;
        }

        //----- Kembalikan sebagai Collection supaya bisa dipakai Laravel Excel
        return collect($data);
    }

    //----- headings(): judul kolom yang tampil di baris pertama Excel
    public function headings(): array
    {
        return [
            'No',
            'No Servis',
            'Pelanggan',
            'Kendaraan',
            'Jasa Servis',
            'Harga',
            'Mekanik',
            'Metode Pembayaran',
            'Tanggal',
            'Status',
        ];
    }
}
