<?php

namespace App\Support;

class RolePermission
{
    //----- Daftar role yang tersedia
    public const ROLES = ['owner', 'admin', 'kasir', 'mekanik'];

    //----- Hak akses per role. Format aksi: 'view:<fitur>' (lihat), 'write:<fitur>' (tambah/edit/hapus),
    //     dan 'export:<fitur>' (download PDF/Excel).
    //     admin mendapat '*' = akses penuh. owner hanya bisa melihat. Export hanya untuk admin & kasir.
    public const AKSES = [
        'admin' => ['*'],

        'owner' => [
            'view:pelanggan',
            'view:kendaraan',
            'view:mekanik',
            'view:jasa_servis',
            'view:supplier',
            'view:servis',
            'view:pembayaran',
            'view:laporan_servis',
            'view:laporan_pendapatan',
            'view:stok_barang',
            'view:pengguna',
        ],

        'kasir' => [
            'view:pelanggan',
            'write:pelanggan',
            'export:pelanggan',
            'view:pembayaran',
            'write:pembayaran',
            'export:pembayaran',
            'view:laporan_pendapatan',
            'export:laporan_pendapatan',
        ],

        'mekanik' => [
            'view:kendaraan',
            'write:kendaraan',
            'view:servis',
            'write:servis',
            'view:laporan_servis',
        ],
    ];

    //----- Cek apakah role boleh melakukan aksi. $role null = ambil dari session('role').
    public static function boleh(string $aksi, ?string $role = null): bool
    {
        $role ??= session('role');

        $akses = self::AKSES[$role] ?? [];

        return in_array('*', $akses, true) || in_array($aksi, $akses, true);
    }

    //----- Abort 403 jika role tidak boleh melakukan aksi.
    public static function cek(string $aksi): void
    {
        abort_unless(self::boleh($aksi), 403);
    }
}
