<?php

namespace App\Exports;

use Illuminate\Support\Collection; //----- Collection: class bawaan Laravel untuk mengelola data array/objek
use Maatwebsite\Excel\Concerns\FromCollection; //----- FromCollection: interface Laravel Excel — sumber data export adalah Collection
use Maatwebsite\Excel\Concerns\WithHeadings; //----- WithHeadings: interface Laravel Excel — menentukan baris judul (header) di Excel

//----- class UsersExport: menyiapkan data gabungan user + mekanik (hasil join1) untuk diekspor ke Excel
//----- Dipakai untuk laporan gabungan pengguna sistem dan data mekanik
class UsersExport implements FromCollection, WithHeadings
{
    //----- __construct(): menerima data users (hasil join users + mekanik dari model database)
    public function __construct(private Collection $users)
    {
    }

    //----- collection(): mengubah data user+mekanik menjadi baris-baris Excel
    //----- $user->nama_user diambil dari tabel users hasil join; $user->nama dari tabel mekanik
    //----- Properti opsional yang kosong ditampilkan sebagai strip
    public function collection(): Collection
    {
        $data = [];
        $no = 1;

        foreach ($this->users as $user) {
            //----- Nama: pakai nama_user dari join, kosong -> nama asli
            $nama = $user->nama;
            if ($user->nama_user != null && $user->nama_user != '') {
                $nama = $user->nama_user;
            }

            $role = '-';
            if ($user->role != null && $user->role != '') {
                $role = $user->role;
            }

            $noHp = '-';
            if ($user->no_hp != null && $user->no_hp != '') {
                $noHp = $user->no_hp;
            }

            $spesialisasi = '-';
            if ($user->spesialisasi != null && $user->spesialisasi != '') {
                $spesialisasi = $user->spesialisasi;
            }

            $status = '-';
            if ($user->status != null && $user->status != '') {
                $status = $user->status;
            }

            $data[] = [
                $no, //----- No urut
                $nama, //----- Nama (user hasil join / fallback nama asli)
                $user->email, //----- Email
                $role, //----- Level/role
                $user->nama, //----- Nama Mekanik
                $noHp, //----- No HP
                $spesialisasi, //----- Spesialisasi
                $status, //----- Status
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
            'Level',
            'Nama Mekanik',
            'No HP',
            'Spesialisasi',
            'Status',
        ];
    }
}
