<?php

namespace App\Exports;

use Illuminate\Support\Collection; //----- Collection: class bawaan Laravel untuk mengelola data array/objek
use Maatwebsite\Excel\Concerns\FromCollection; //----- FromCollection: interface Laravel Excel — sumber data export adalah Collection
use Maatwebsite\Excel\Concerns\WithHeadings; //----- WithHeadings: interface Laravel Excel — menentukan baris judul (header) di Excel

//----- class PenggunaExport: menyiapkan data pengguna/pengguna sistem untuk diekspor ke Excel
class PenggunaExport implements FromCollection, WithHeadings
{
    //----- __construct(): menerima data rows (hasil query users) saat class dipanggil dari controller
    public function __construct(private iterable $rows)
    {
    }

    //----- collection(): mengubah data pengguna menjadi baris-baris Excel
    //----- is_active 1/0 diubah jadi teks Aktif/Tidak Aktif
    public function collection(): Collection
    {
        $data = [];
        $no = 1;

        foreach ($this->rows as $row) {
            //----- Status disimpan sebagai angka, diubah jadi teks yang mudah dibaca
            $status = 'Tidak Aktif';
            if ($row->is_active == 1) {
                $status = 'Aktif';
            }

            $data[] = [
                $no, //----- No urut
                $row->nama, //----- Nama pengguna
                $row->email, //----- Email
                $row->role, //----- Role (admin/mekanik/dll)
                $row->no_hp, //----- No HP
                $status, //----- Status aktif/tidak
                $row->created_at, //----- Tanggal bergabung
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
            'Nama',
            'Email',
            'Role',
            'No HP',
            'Status',
            'Tanggal Bergabung',
        ];
    }
}
