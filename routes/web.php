<?php

use Illuminate\Support\Facades\Route;

// Login & Register — halaman publik tanpa login
Route::get('/', 'App\Http\Controllers\LoginController@index'); // halaman login
Route::get('/login', 'App\Http\Controllers\LoginController@index'); // halaman login
Route::post('/login', 'App\Http\Controllers\LoginController@aksilogin'); // proses login
Route::get('/girasya', 'App\Http\Controllers\LoginController@register'); // halaman register
Route::post('/input', 'App\Http\Controllers\LoginController@simpanRegister'); // proses register

// Home & Logout — setelah login
Route::get('/home', 'App\Http\Controllers\LoginController@home'); // dashboard utama
Route::get('/logout', 'App\Http\Controllers\LoginController@logout'); // logout user

//Mekanik — owner & admin (admin: lihat + tambah/edit, hapus khusus owner)
Route::middleware('role:owner,admin')->group(function () {
    Route::get('/tkry', 'App\Http\Controllers\MekanikController@tkry'); // menampilkan form tambah mekanik
    Route::post('/aksi_tkry', 'App\Http\Controllers\MekanikController@aksi_tkry'); // menyimpan data mekanik baru
    Route::get('/mekanik', 'App\Http\Controllers\MekanikController@karyawan'); // menampilkan daftar mekanik
    Route::get('/edit_mekanik/{id}', 'App\Http\Controllers\MekanikController@editMekanik'); // menampilkan form edit mekanik
    Route::post('/edit_mekanik_aksi/{id}', 'App\Http\Controllers\MekanikController@updateMekanik'); // menyimpan perubahan mekanik
    Route::get('/mekanik/pdf', 'App\Http\Controllers\MekanikController@pdfMekanik'); // download PDF mekanik
    Route::get('/mekanik/excel', 'App\Http\Controllers\MekanikController@excelMekanik'); // download Excel mekanik
});
Route::post('/hapus_mekanik/{id}', 'App\Http\Controllers\MekanikController@hapusMekanik') // menghapus mekanik
    ->middleware('role:owner');

// Pelanggan — owner & kasir (lihat + tambah); edit/hapus khusus owner
Route::middleware('role:owner,kasir')->group(function () {
    Route::get('/pelanggan', 'App\Http\Controllers\PelangganController@pelanggan'); // menampilkan daftar pelanggan
    Route::get('/tplg', 'App\Http\Controllers\PelangganController@tplg'); // menampilkan form tambah pelanggan
    Route::post('/aksi_tplg', 'App\Http\Controllers\PelangganController@aksi_tplg'); // menyimpan data pelanggan baru
});
Route::middleware('role:owner')->group(function () {
    Route::get('/edit_pelanggan/{id}', 'App\Http\Controllers\PelangganController@editPelanggan'); // menampilkan form edit pelanggan
    Route::post('/edit_pelanggan_aksi/{id}', 'App\Http\Controllers\PelangganController@updatePelanggan'); // menyimpan perubahan pelanggan
    Route::post('/hapus_pelanggan/{id}', 'App\Http\Controllers\PelangganController@hapusPelanggan'); // menghapus pelanggan
});

// Kendaraan — owner & karyawan (lihat + input); edit/hapus khusus owner
Route::middleware('role:owner,karyawan')->group(function () {
    Route::get('/kendaraan', 'App\Http\Controllers\KendaraanController@kendaraan'); // menampilkan daftar kendaraan
    Route::get('/tambah_kendaraan', 'App\Http\Controllers\KendaraanController@tambahKendaraan'); // menampilkan form tambah kendaraan
    Route::post('/tambah_kendaraan_aksi', 'App\Http\Controllers\KendaraanController@simpanKendaraan'); // menyimpan data kendaraan baru
});
Route::middleware('role:owner')->group(function () {
    Route::get('/edit_kendaraan/{id}', 'App\Http\Controllers\KendaraanController@editKendaraan'); // menampilkan form edit kendaraan
    Route::post('/edit_kendaraan_aksi/{id}', 'App\Http\Controllers\KendaraanController@updateKendaraan'); // menyimpan perubahan kendaraan
    Route::post('/hapus_kendaraan/{id}', 'App\Http\Controllers\KendaraanController@hapusKendaraan'); // menghapus kendaraan
});

// Jasa Servis — owner & admin (lihat + tambah); edit/hapus khusus owner
Route::middleware('role:owner,admin')->group(function () {
    Route::get('/jasa-servis', 'App\Http\Controllers\JasaServisController@jasaServis'); // menampilkan daftar jasa servis
    Route::get('/tambah_jasa_servis', 'App\Http\Controllers\JasaServisController@tambahJasaServis'); // menampilkan form tambah jasa servis
    Route::post('/tambah_jasa_servis_aksi', 'App\Http\Controllers\JasaServisController@simpanJasaServis'); // menyimpan data jasa servis baru
});
Route::middleware('role:owner')->group(function () {
    Route::get('/edit_jasa_servis/{id}', 'App\Http\Controllers\JasaServisController@editJasaServis'); // menampilkan form edit jasa servis
    Route::post('/edit_jasa_servis_aksi/{id}', 'App\Http\Controllers\JasaServisController@updateJasaServis'); // menyimpan perubahan jasa servis
    Route::post('/hapus_jasa_servis/{id}', 'App\Http\Controllers\JasaServisController@hapusJasaServis'); // menghapus jasa servis
});

// Supplier — khusus owner
Route::middleware('role:owner')->group(function () {
    Route::get('/supplier', 'App\Http\Controllers\SupplierController@supplier'); // menampilkan daftar supplier
    Route::get('/tambah_supplier', 'App\Http\Controllers\SupplierController@tambahSupplier'); // menampilkan form tambah supplier
    Route::post('/tambah_supplier_aksi', 'App\Http\Controllers\SupplierController@simpanSupplier'); // menyimpan data supplier baru
    Route::get('/edit_supplier/{id}', 'App\Http\Controllers\SupplierController@editSupplier'); // menampilkan form edit supplier
    Route::post('/edit_supplier_aksi/{id}', 'App\Http\Controllers\SupplierController@updateSupplier'); // menyimpan perubahan supplier
    Route::post('/hapus_supplier/{id}', 'App\Http\Controllers\SupplierController@hapusSupplier'); // menghapus supplier
});

// Servis — owner & kasir (lihat + tambah); edit/hapus khusus owner
Route::middleware('role:owner,kasir')->group(function () {
    Route::get('/servis', 'App\Http\Controllers\ServisController@servis'); // menampilkan daftar transaksi servis
    Route::get('/tambah_servis', 'App\Http\Controllers\ServisController@tambahServis'); // menampilkan form tambah servis
    Route::post('/tambah_servis_aksi', 'App\Http\Controllers\ServisController@simpanServis'); // menyimpan transaksi servis baru
});
Route::middleware('role:owner')->group(function () {
    Route::get('/edit_servis/{id}', 'App\Http\Controllers\ServisController@editServis'); // menampilkan form edit servis
    Route::post('/edit_servis_aksi/{id}', 'App\Http\Controllers\ServisController@updateServis'); // menyimpan perubahan servis
    Route::post('/hapus_servis/{id}', 'App\Http\Controllers\ServisController@hapusServis'); // menghapus servis
});

// Pembayaran — khusus owner
Route::middleware('role:owner')->group(function () {
    Route::get('/pembayaran', 'App\Http\Controllers\PembayaranController@pembayaran'); // menampilkan daftar pembayaran
    Route::get('/edit_pembayaran/{id}', 'App\Http\Controllers\PembayaranController@editPembayaran'); // menampilkan form edit pembayaran
    Route::post('/edit_pembayaran_aksi/{id}', 'App\Http\Controllers\PembayaranController@updatePembayaran'); // menyimpan perubahan pembayaran
    Route::post('/hapus_pembayaran/{id}', 'App\Http\Controllers\PembayaranController@hapusPembayaran'); // menghapus pembayaran
});

// Laporan — owner, admin, dan mekanik (mekanik hanya view; export dibatasi di controller)
Route::get('/laporan-servis', 'App\Http\Controllers\LaporanController@laporanServis') // menampilkan laporan servis + export
    ->middleware('role:owner,admin,mekanik');
Route::get('/laporan-pendapatan', 'App\Http\Controllers\LaporanController@laporanPendapatan') // menampilkan laporan pendapatan + export
    ->middleware('role:owner,admin');

// Stok Barang — owner & admin (lihat + tambah stok masuk); stok keluar khusus owner
Route::middleware('role:owner,admin')->group(function () {
    Route::get('/stok-barang', 'App\Http\Controllers\StokBarangController@stokBarang'); // menampilkan rekap stok barang
    Route::get('/input_stok_barang', 'App\Http\Controllers\StokBarangController@inputStokBarang'); // menampilkan form kelola stok
    Route::post('/stok-barang_tambah', 'App\Http\Controllers\StokBarangController@stokMasuk'); // menyimpan transaksi stok masuk
});
Route::post('/stok-barang_keluar/{id}', 'App\Http\Controllers\StokBarangController@stokKeluar') // menyimpan transaksi stok keluar
    ->middleware('role:owner');

// Pengguna — owner & admin (admin hanya lihat status); kelola user khusus owner
Route::middleware('role:owner,admin')->group(function () {
    Route::get('/pengguna', 'App\Http\Controllers\PenggunaController@pengguna'); // menampilkan daftar pengguna
});
Route::middleware('role:owner')->group(function () {
    Route::get('/tambah_users', 'App\Http\Controllers\PenggunaController@tambahPengguna'); // menampilkan form tambah pengguna
    Route::post('/tambah_users_aksi', 'App\Http\Controllers\PenggunaController@simpanPengguna'); // menyimpan pengguna baru
    Route::get('/edit_users/{id}', 'App\Http\Controllers\PenggunaController@editPengguna'); // menampilkan form edit pengguna
    Route::post('/edit_users_aksi/{id}', 'App\Http\Controllers\PenggunaController@updatePengguna'); // menyimpan perubahan pengguna
    Route::post('/hapus_users/{id}', 'App\Http\Controllers\PenggunaController@hapusPengguna'); // menghapus pengguna
});

// Export PDF/Excel — hak khusus owner & admin (untuk laporan)
Route::middleware('role:owner,admin')->group(function () {
    Route::get('/pelanggan/pdf', 'App\Http\Controllers\PelangganController@pdfPelanggan');
    Route::get('/pelanggan/excel', 'App\Http\Controllers\PelangganController@excelPelanggan');
    Route::get('/kendaraan/pdf', 'App\Http\Controllers\KendaraanController@pdfKendaraan');
    Route::get('/kendaraan/excel', 'App\Http\Controllers\KendaraanController@excelKendaraan');
    Route::get('/jasa-servis/pdf', 'App\Http\Controllers\JasaServisController@pdfJasaServis');
    Route::get('/jasa-servis/excel', 'App\Http\Controllers\JasaServisController@excelJasaServis');
    Route::get('/supplier/pdf', 'App\Http\Controllers\SupplierController@pdfSupplier');
    Route::get('/supplier/excel', 'App\Http\Controllers\SupplierController@excelSupplier');
    Route::get('/servis/pdf', 'App\Http\Controllers\ServisController@pdfServis');
    Route::get('/servis/excel', 'App\Http\Controllers\ServisController@excelServis');
    Route::get('/pembayaran/pdf', 'App\Http\Controllers\PembayaranController@pdfPembayaran');
    Route::get('/pembayaran/excel', 'App\Http\Controllers\PembayaranController@excelPembayaran');
    Route::get('/stok-barang/pdf', 'App\Http\Controllers\StokBarangController@pdfStokBarang');
    Route::get('/stok-barang/excel', 'App\Http\Controllers\StokBarangController@excelStokBarang');
    Route::get('/pengguna/pdf', 'App\Http\Controllers\PenggunaController@pdfPengguna');
    Route::get('/pengguna/excel', 'App\Http\Controllers\PenggunaController@excelPengguna');
});

// Profil — semua role yang sudah login
Route::get('/profil', 'App\Http\Controllers\ProfilController@profil'); // menampilkan halaman profil
Route::post('/profil', 'App\Http\Controllers\ProfilController@updateProfil'); // menyimpan perubahan profil
