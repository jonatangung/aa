<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\MekanikExport;
use App\Support\RolePermission;
use Maatwebsite\Excel\Facades\Excel;

//----- MekanikController: menangani CRUD data mekanik beserta export PDF/Excel
class MekanikController extends Controller
{
    // ==================== MEKANIK ====================

    //----- karyawan(): Menampilkan halaman daftar mekanik. Mengirim data $karyawan ke view mekanik
    public function karyawan()
    {
        RolePermission::cek('view:mekanik');

        // $karyawan ini yang dipakai di view mekanik sebagai $karyawan
        return view('mekanik', ['karyawan' => $this->mekanikData()]);
    }

    //----- mekanikData(): Mengambil seluruh data mekanik dari database (private). get() = ambil semua data
    private function mekanikData()
    {
        return DB::table('mekanik')->get();
    }

    //----- tkry(): Menampilkan form tambah mekanik baru
    public function tkry()
    {
        RolePermission::cek('write:mekanik');

        return view('input_mekanik');
    }

    //----- aksi_tkry(): Menyimpan data mekanik baru dan mengaitkannya dengan user yang login
    public function aksi_tkry(Request $request)
    {
        RolePermission::cek('write:mekanik');

        $user = DB::table('users')->where('id', session('id_user'))->first();

        if (!$user) {
            return redirect('/tkry');
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'no_hp' => 'nullable|string|max:20',
            'spesialisasi' => 'nullable|string|max:255',
        ]);

        $data = [
            'user_id' => $user->id,
            'nama' => $validated['nama'],
            'no_hp' => $validated['no_hp'],
            'spesialisasi' => $validated['spesialisasi'],
            'status' => 'aktif',
        ];

        DB::table('mekanik')->insert($data);

        return redirect('/mekanik');
    }

    //----- editMekanik(): Menampilkan form edit mekanik berdasarkan id. Mengirim data $row ke view edit_mekanik. where() = cari data, first() = ambil satu data
    public function editMekanik($id)
    {
        RolePermission::cek('write:mekanik');

        $row = DB::table('mekanik')->where('id', $id)->first();
        abort_if($row === null, 404);

        // $row ini yang dipakai di view edit_mekanik sebagai $row
        return view('edit_mekanik', ['row' => $row]);
    }

    //----- updateMekanik(): Memperbarui data mekanik yang dipilih. update() = ubah data di database, redirect() = pindah halaman
    public function updateMekanik(Request $request, $id)
    {
        RolePermission::cek('write:mekanik');

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'no_hp' => 'nullable|string|max:20',
            'spesialisasi' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        DB::table('mekanik')->where('id', $id)->update([
            'nama' => $validated['nama'],
            'no_hp' => $validated['no_hp'],
            'spesialisasi' => $validated['spesialisasi'],
            'status' => $validated['status'],
            'updated_at' => now(),
        ]);
        return redirect('/mekanik');
    }

    //----- hapusMekanik(): Menghapus data mekanik berdasarkan id. delete() = hapus data dari database
    public function hapusMekanik($id)
    {
        RolePermission::cek('write:mekanik');

        DB::table('mekanik')->where('id', $id)->delete();
        return redirect('/mekanik');
    }

    //----- pdfMekanik(): Mengunduh laporan data mekanik dalam format PDF
    public function pdfMekanik()
    {
        RolePermission::cek('view:mekanik');

        $export = new MekanikExport($this->mekanikData());

        $this->pdfTable('Laporan Data Mekanik', 'laporan-mekanik', $export->headings(), $export->collection()->all());
    }

    //----- excelMekanik(): Mengunduh laporan data mekanik dalam format Excel (.xlsx)
    public function excelMekanik()
    {
        RolePermission::cek('view:mekanik');

        return Excel::download(
            new MekanikExport($this->mekanikData()),
            'laporan-mekanik-' . date('Y-m-d') . '.xlsx'
        );
    }
}
