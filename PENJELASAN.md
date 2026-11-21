# PENJELASAN KODE PROJECT - MODUL MEKANIK

---

## 1. VIEW - mekanik.blade.php

### Fungsi Halaman
Halaman ini menampilkan daftar semua mekanik dalam bentuk tabel. Ada tombol untuk tambah mekanik baru, print, download PDF, dan export Excel.

### Penjelasan Bagian Per Bagian

**Bagian 1: Judul dan Tombol Aksi (baris 5-14)**
```php
<h1 class="mb-3">Data Mekanik</h1>
```
- Menampilkan judul halaman.

```php
<a href="/tkry" class="btn btn-secondary">Tambah Mekanik</a>
```
- Tombol untuk ke halaman tambah mekanik baru.

```php
<button type="button" class="btn btn-secondary btn-sm" onclick="window.print()">Print Window</button>
```
- Tombol untuk print halaman browser.

```php
<a href="/mekanik/pdf" target="_blank" class="btn btn-danger btn-sm">Download PDF</a>
```
- Tombol untuk download laporan PDF (buka di tab baru).

```php
<a href="/mekanik/excel" class="btn btn-success btn-sm">Export Excel</a>
```
- Tombol untuk download laporan Excel.

**Bagian 2: Tabel Daftar Mekanik (baris 16-52)**
```php
<table class="table table-bordered table-striped align-middle mt-3">
```
- Membuat tabel dengan class Bootstrap untuk tampilan rapi.

```php
<thead class="table-dark text-center">
    <tr>
        <th>No</th>
        <th>Nama</th>
        <th>No HP</th>
        <th>Spesialisasi</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>
</thead>
```
- Header tabel: kolom nomor, nama, no HP, spesialisasi, status, dan aksi.

```php
<?php $no = 1; ?>
```
- Inisialisasi variabel $no = 1 untuk nomor urut tabel.

```php
@foreach ($karyawan as $value)
```
- @foreach: perulangan Blade untuk menampilkan setiap baris data.
- $karyawan: variabel yang dikirim dari controller (method karyawan()), berisi semua data mekanik dari database.
- $value: variabel satu baris data di setiap putaran foreach (jadi $value adalah satu mekanik).

```php
<tr>
    <td>{{ $no }}</td>
    <?php $no++; ?>
```
- Menampilkan nomor urut.
- {{ }}: untuk menampilkan variabel ke layar (diescape otomatis untuk keamanan).
- $no++: menambah nilai $no setiap baris (1, 2, 3, dst).

```php
<td>{{ $value->nama }}</td>
```
- Menampilkan nama mekanik.
- $value->nama: mengambil kolom 'nama' dari data mekanik saat ini.

```php
<td>
    <?php if ($value->no_hp == '') { ?>
        -
    <?php } else { ?>
        {{ $value->no_hp }}
    <?php } ?>
</td>
```
- Menampilkan no HP, tapi kalau kosong tampilkan tanda "-".
- <?php if (...) { ?> ... <?php } else { ?> ... <?php } ?>: kondisi PHP biasa (bukan Blade).
- Cek apakah no_hp kosong string, kalau ya tampilkan "-", kalau tidak tampilkan no_hp asli.

```php
<td>
    <?php if ($value->spesialisasi == '') { ?>
        -
    <?php } else { ?>
        {{ $value->spesialisasi }}
    <?php } ?>
</td>
```
- Sama seperti no HP, tapi untuk spesialisasi.

```php
<td>{{ $value->status }}</td>
```
- Menampilkan status mekanik (aktif/nonaktif) apa adanya.

```php
<td>
    <a href="/edit_mekanik/{{ $value->id }}" class="btn btn-sm btn-warning">Edit</a>
    <form action="/hapus_mekanik/{{ $value->id }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
    </form>
</td>
```
- Tombol Edit: link ke halaman edit dengan id mekanik.
- Tombol Delete: form POST ke route hapus_mekanik dengan id.
- @csrf: token keamanan Laravel untuk mencegah CSRF attack.

```php
@endforeach
```
- Penutup perulangan foreach.

**Variabel Penting:**
- $karyawan: dikirim dari MekanikController method karyawan(), diisi dari DB::table('mekanik')->get().
- $value: satu baris dari $karyawan di setiap putaran foreach.
- $no: variabel counter untuk nomor urut, dimulai dari 1.

---

## 2. VIEW - input_mekanik.blade.php

### Fungsi Halaman
Form untuk menambahkan mekanik baru ke database.

### Penjelasan Bagian Per Bagian

**Bagian 1: Judul (baris 5)**
```php
<h1 class="mb-4 fw-bold">Input Mekanik</h1>
```
- Judul halaman input mekanik.

**Bagian 2: Form Input (baris 7-29)**
```php
<form action="/aksi_tkry" method="POST" class="no-print">
    @csrf
```
- Form dengan method POST ke route /aksi_tkry.
- @csrf: token keamanan Laravel.

```php
<div class="mb-3">
    <label class="fw-semibold">Nama Mekanik:</label>
    <input type="text" name="nama" class="form-control" required>
</div>
```
- Input nama mekanik (wajib diisi karena atribut required).

```php
<div class="mb-3">
    <label class="fw-semibold">No HP:</label>
    <input type="text" name="no_hp" class="form-control">
</div>
```
- Input nomor HP (opsional, tidak ada required).

```php
<div class="mb-3">
    <label class="fw-semibold">Spesialisasi:</label>
    <input type="text" name="spesialisasi" class="form-control">
</div>
```
- Input spesialisasi mekanik (opsional).

```php
<button type="submit" class="btn btn-primary px-4">Simpan</button>
<a href="/mekanik" class="btn btn-secondary px-4">Kembali</a>
```
- Tombol Simpan untuk submit form.
- Tombol Kembali untuk kembali ke halaman daftar mekanik.

---

## 3. VIEW - edit_mekanik.blade.php

### Fungsi Halaman
Form untuk mengedit data mekanik yang sudah ada.

### Penjelasan Bagian Per Bagian

**Bagian 1: Judul (baris 5)**
```php
<h1 class="mb-4 fw-bold">Edit Mekanik</h1>
```
- Judul halaman edit mekanik.

**Bagian 2: Form Edit (baris 7-37)**
```php
<form action="/edit_mekanik_aksi/{{ $row->id }}" method="POST">
    @csrf
```
- Form dengan method POST ke route edit_mekanik_aksi dengan id mekanik.
- $row: variabel yang dikirim dari controller (method editMekanik()), berisi data mekanik yang akan diedit.

```php
<div class="mb-3">
    <label class="fw-semibold">Nama</label>
    <input type="text" name="nama" value="{{ $row->nama }}" class="form-control" required>
</div>
```
- Input nama dengan value awal dari data yang sudah ada ({{ $row->nama }}).

```php
<div class="mb-3">
    <label class="fw-semibold">No HP</label>
    <input type="text" name="no_hp" value="{{ $row->no_hp }}" class="form-control">
</div>
```
- Input no HP dengan value awal.

```php
<div class="mb-3">
    <label class="fw-semibold">Spesialisasi</label>
    <input type="text" name="spesialisasi" value="{{ $row->spesialisasi }}" class="form-control">
</div>
```
- Input spesialisasi dengan value awal.

```php
<div class="mb-3">
    <label class="fw-semibold">Status</label>
    <select name="status" class="form-control" required>
        <option value="aktif" <?php if ($row->status == 'aktif') { ?>selected<?php } ?>>Aktif</option>
        <option value="nonaktif" <?php if ($row->status == 'nonaktif') { ?>selected<?php } ?>>Nonaktif</option>
    </select>
</div>
```
- Dropdown select untuk status (aktif/nonaktif).
- <?php if ($row->status == 'aktif') { ?>selected<?php } ?>: menandai option yang sedang aktif dengan atribut selected.
- Ini cara manual tanpa directive @selected Blade.

```php
<button class="btn btn-warning" type="submit">Perbarui</button>
<a href="/mekanik" class="btn btn-secondary">Kembali</a>
```
- Tombol Perbarui untuk submit form.
- Tombol Kembali ke halaman daftar mekanik.

**Variabel Penting:**
- $row: dikirim dari MekanikController method editMekanik($id), diisi dari DB::table('mekanik')->where('id', $id)->first().

---

## 4. CONTROLLER - MekanikController.php

### Fungsi Controller
MekanikController menangani semua operasi CRUD (Create, Read, Update, Delete) untuk data mekanik, plus export PDF dan Excel.

### Penjelasan Setiap Method

**Method karyawan() (baris 17-22)**
```php
public function karyawan()
{
    if (!session('u')) return redirect('/');

    return view('mekanik', ['karyawan' => $this->mekanikData()]);
}
```
- Tugas: menampilkan halaman daftar mekanik.
- Parameter: tidak ada.
- if (!session('u')): cek apakah user sudah login (session 'u' ada). Kalau tidak, redirect ke halaman login.
- return view('mekanik', ['karyawan' => $this->mekanikData()]): mengirim data ke view mekanik.blade.php.
- $karyawan di view berasal dari array ['karyawan' => ...] di sini.
- $this->mekanikData(): memanggil method private untuk mengambil data dari database.

**Method mekanikData() (baris 25-28)**
```php
private function mekanikData()
{
    return DB::table('mekanik')->get();
}
```
- Tugas: mengambil semua data mekanik dari database.
- DB::table('mekanik'): memilih tabel 'mekanik'.
- ->get(): mengambil semua baris data (equivalent SELECT * FROM mekanik).
- private: hanya bisa dipanggil dari dalam class ini saja.

**Method tkry() (baris 31-36)**
```php
public function tkry()
{
    if (!session('u')) return redirect('/');

    return view('input_mekanik');
}
```
- Tugas: menampilkan form tambah mekanik baru.
- Tidak perlu mengirim data ke view, cukup tampilkan form saja.

**Method aksi_tkry() (baris 39-60)**
```php
public function aksi_tkry(Request $request)
{
    if (!session('u')) return redirect('/');

    $user = (new database)->tarik('users', ['nama' => session('u')]);

    if (!$user) {
        return redirect('/tkry');
    }

    $data = [
        'user_id' => $user->id,
        'nama' => $request->nama,
        'no_hp' => $request->no_hp,
        'spesialisasi' => $request->spesialisasi,
        'status' => 'aktif',
    ];

    DB::table('mekanik')->insert($data);

    return redirect('/mekanik');
}
```
- Tugas: menyimpan data mekanik baru ke database.
- Parameter: $request (objek Request berisi data dari form).
- (new database)->tarik('users', ['nama' => session('u')]): mengambil data user yang sedang login dari tabel users.
- $request->nama: mengambil nilai input 'nama' dari form.
- DB::table('mekanik')->insert($data): menyimpan data baru ke tabel mekanik.
- redirect('/mekanik'): setelah berhasil, redirect ke halaman daftar mekanik.

**Method editMekanik($id) (baris 63-70)**
```php
public function editMekanik($id)
{
    if (!session('u')) return redirect('/');

    return view('edit_mekanik', [
        'row' => DB::table('mekanik')->where('id', $id)->first(),
    ]);
}
```
- Tugas: menampilkan form edit mekanik.
- Parameter: $id (id mekanik yang akan diedit).
- DB::table('mekanik')->where('id', $id)->first(): mencari mekanik dengan id tertentu, ambil satu baris saja.
- $row di view berasal dari array ['row' => ...] di sini.

**Method updateMekanik() (baris 73-85)**
```php
public function updateMekanik(Request $request, $id)
{
    if (!session('u')) return redirect('/');

    DB::table('mekanik')->where('id', $id)->update([
        'nama' => $request->nama,
        'no_hp' => $request->no_hp,
        'spesialisasi' => $request->spesialisasi,
        'status' => $request->status,
        'updated_at' => now(),
    ]);
    return redirect('/mekanik');
}
```
- Tugas: memperbarui data mekanik di database.
- Parameter: $request (data form), $id (id mekanik).
- DB::table('mekanik')->where('id', $id)->update([...]): update baris dengan id tertentu.
- now(): fungsi Laravel untuk mengambil waktu sekarang.

**Method hapusMekanik($id) (baris 88-94)**
```php
public function hapusMekanik($id)
{
    if (!session('u')) return redirect('/');

    DB::table('mekanik')->where('id', $id)->delete();
    return redirect('/mekanik');
}
```
- Tugas: menghapus data mekanik.
- Parameter: $id (id mekanik yang akan dihapus).
- DB::table('mekanik')->where('id', $id)->delete(): hapus baris dengan id tertentu.

**Method pdfMekanik() (baris 97-104)**
```php
public function pdfMekanik()
{
    if (!session('u')) return redirect('/');

    $export = new MekanikExport($this->mekanikData());

    $this->pdfTable('Laporan Data Mekanik', 'laporan-mekanik', $export->headings(), $export->collection()->all());
}
```
- Tugas: generate dan download laporan PDF.
- MekanikExport: class untuk export data (dari package Maatwebsite/Excel).
- $this->pdfTable(): method dari controller induk untuk generate PDF.

**Method excelMekanik() (baris 107-115)**
```php
public function excelMekanik()
{
    if (!session('u')) return redirect('/');

    return Excel::download(
        new MekanikExport($this->mekanikData()),
        'laporan-mekanik-' . date('Y-m-d') . '.xlsx'
    );
}
```
- Tugas: download laporan Excel.
- Excel::download(): fungsi dari package Maatwebsite/Excel untuk download file Excel.
- date('Y-m-d'): format tanggal sekarang (misal: 2026-11-16).

### Fungsi Laravel yang Dipakai
- DB::table($nama_tabel): memilih tabel database.
- ->get(): mengambil semua baris.
- ->where($kolom, $nilai): filter data.
- ->first(): mengambil satu baris pertama.
- ->insert($data): menyimpan data baru.
- ->update($data): memperbarui data.
- ->delete(): menghapus data.
- session('kunci'): mengambil nilai session.
- redirect($url): mengarahkan ke URL lain.
- view($nama_view, $data): menampilkan view dengan data.
- $request->nama_input: mengambil nilai dari form.
- now(): mengambil waktu sekarang.

---

## 5. MODEL - database.php

### Fungsi Model
Class database adalah helper generik untuk operasi CRUD ke tabel mana pun. Tidak terikat ke satu tabel tertentu, nama tabel dikirim sebagai parameter.

### Penjelasan Method yang Dipakai MekanikController

**Method tarik() (baris 94-99)**
```php
public function tarik($table, $where)
{
    return DB::table($table)->where($where)->first();
}
```
- Tugas: mengambil satu baris data dari tabel berdasarkan kondisi.
- Dipakai di aksi_tkry() untuk mengambil data user yang login.
- $table: nama tabel (misal: 'users').
- $where: array kondisi (misal: ['nama' => 'admin']).
- first(): ambil satu baris saja.

Method lain di database.php (tampil, tambah, ubah, hapus) tidak dipakai langsung di MekanikController, tapi bisa dipakai modul lain.

---

## 6. ROUTE - web.php

### Route untuk Modul Mekanik
```php
Route::get('/tkry', 'App\Http\Controllers\MekanikController@tkry');
```
- URL /tkry -> MekanikController method tkry() (tampilkan form tambah mekanik).

```php
Route::post('/aksi_tkry', 'App\Http\Controllers\MekanikController@aksi_tkry');
```
- URL /aksi_tkry (POST) -> MekanikController method aksi_tkry() (simpan mekanik baru).

```php
Route::get('/mekanik', 'App\Http\Controllers\MekanikController@karyawan');
```
- URL /mekanik -> MekanikController method karyawan() (tampilkan daftar mekanik).

```php
Route::get('/edit_mekanik/{id}', 'App\Http\Controllers\MekanikController@editMekanik');
```
- URL /edit_mekanik/{id} -> MekanikController method editMekanik($id) (tampilkan form edit).

```php
Route::post('/edit_mekanik_aksi/{id}', 'App\Http\Controllers\MekanikController@updateMekanik');
```
- URL /edit_mekanik_aksi/{id} (POST) -> MekanikController method updateMekanik() (update data).

```php
Route::post('/hapus_mekanik/{id}', 'App\Http\Controllers\MekanikController@hapusMekanik');
```
- URL /hapus_mekanik/{id} (POST) -> MekanikController method hapusMekanik() (hapus data).

```php
Route::get('/mekanik/pdf', 'App\Http\Controllers\MekanikController@pdfMekanik');
```
- URL /mekanik/pdf -> MekanikController method pdfMekanik() (download PDF).

```php
Route::get('/mekanik/excel', 'App\Http\Controllers\MekanikController@excelMekanik');
```
- URL /mekanik/excel -> MekanikController method excelMekanik() (download Excel).

---

## 7. ALUR LENGKAP - DATA MEKANIK

### Alur Menampilkan Daftar Mekanik
1. User buka URL `/mekanik` di browser.
2. Route mengarah ke `MekanikController@karyawan`.
3. Method `karyawan()` cek session login, lalu panggil `mekanikData()`.
4. `mekanikData()` query database: `DB::table('mekanik')->get()`.
5. Data dikirim ke view: `view('mekanik', ['karyawan' => $data])`.
6. View `mekanik.blade.php` menampilkan data dalam tabel dengan `@foreach`.
7. Tampilan muncul di browser.

### Alur Menambah Mekanik Baru
1. User klik tombol "Tambah Mekanik", buka URL `/tkry`.
2. Route mengarah ke `MekanikController@tkry`.
3. Method `tkry()` menampilkan view `input_mekanik.blade.php`.
4. User isi form dan klik "Simpan" (POST ke `/aksi_tkry`).
5. Route mengarah ke `MekanikController@aksi_tkry`.
6. Method `aksi_tkry()` ambil data user login, ambil data form, lalu `insert` ke database.
7. Redirect ke `/mekanik`, daftar mekanik muncul dengan data baru.

### Alur Edit Mekanik
1. User klik tombol "Edit" pada mekanik tertentu, buka URL `/edit_mekanik/{id}`.
2. Route mengarah ke `MekanikController@editMekanik($id)`.
3. Method `editMekanik()` query data mekanik berdasarkan id, kirim ke view.
4. View `edit_mekanik.blade.php` menampilkan form dengan data yang sudah ada.
5. User ubah data dan klik "Perbarui" (POST ke `/edit_mekanik_aksi/{id}`).
6. Route mengarah ke `MekanikController@updateMekanik()`.
7. Method `updateMekanik()` update data di database.
8. Redirect ke `/mekanik`, data sudah berubah.

### Alur Hapus Mekanik
1. User klik tombol "Delete" pada mekanik tertentu (POST ke `/hapus_mekanik/{id}`).
2. Route mengarah ke `MekanikController@hapusMekanik($id)`.
3. Method `hapusMekanik()` hapus data dari database.
4. Redirect ke `/mekanik`, data sudah hilang.

---

## 8. RINGKASAN PERUBAHAN

### File yang Diubah:
1. `resources/views/mekanik.blade.php`
2. `resources/views/edit_mekanik.blade.php`

### Apa yang Disederhanakan:

**mekanik.blade.php:**
- Hapus `@if ($value->nama)` dan `@endif` - sekarang semua baris ditampilkan tanpa filter.
- Ganti `<?= $no++ ?>` dengan `{{ $no }}` dan `<?php $no++; ?>` (lebih jelas).
- Ganti `{{ $value->no_hp ?: '-' }}` dengan if biasa untuk cek kosong.
- Ganti `{{ $value->spesialisasi ?: '-' }}` dengan if biasa.
- Hapus `ucfirst($value->status)` - status ditampilkan apa adanya.

**edit_mekanik.blade.php:**
- Hapus directive `@selected()` Blade.
- Ganti dengan `<?php if ($row->status == 'aktif') { ?>selected<?php } ?>` (PHP biasa).

**input_mekanik.blade.php:**
- Tidak ada perubahan, sudah sederhana.

**MekanikController.php:**
- Tidak ada perubahan, sudah pakai cara dasar (DB::table, redirect, view).

**database.php:**
- Tidak ada perubahan, sudah punya komentar yang jelas.

---

## 9. HASIL VALIDASI

✅ php -l: Tidak ada syntax error di semua file yang diubah.
✅ Cache cleared: view, cache, config, route sudah dibersihkan.
✅ route:list: Semua route modul Mekanik masih ada dan benar.
✅ Sisa @if/@else/@endif: 0 di view Mekanik.
✅ Sisa operator ?: dan ucfirst(): 0 di view Mekanik.
✅ Sisa <?= : 0 di view Mekanik.
✅ Pasangan kurung { }: Semua if sudah punya pasangan buka dan tutup.

---

## 10. HAL YANG PERLU DICEK MANUAL

Silakan buka aplikasi dan cek:
1. Halaman daftar mekanik (/mekanik) - apakah tampilan tabel normal?
2. Tombol tambah mekanik - apakah form input muncul?
3. Simpan mekanik baru - apakah data tersimpan dan muncul di tabel?
4. Tombol edit mekanik - apakah form edit muncul dengan data yang benar?
5. Update mekanik - apakah perubahan tersimpan?
6. Tombol delete mekanik - apakah data terhapus?
7. Tombol PDF dan Excel - apakah download berfungsi?
8. Pastikan nomor urut di tabel berjalan 1, 2, 3, dst.
9. Pastikan tanda "-" muncul kalau no_hp atau spesialisasi kosong.
10. Pastikan status ditampilkan "aktif" atau "nonaktif" (huruf kecil semua).

---

**Modul Mekanik selesai disederhanakan!** 🎉
