<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\database;
use App\Exports\PelangganExport;
use App\Support\RolePermission;
use Maatwebsite\Excel\Facades\Excel;

//----- PelangganController: menangani CRUD data pelanggan beserta export PDF/Excel
class PelangganController extends Controller
{
    // ==================== PELANGGAN ====================

    //----- pelanggan(): Menampilkan halaman daftar pelanggan (dengan filter tanggal). Mengirim data $pelanggan ke view pelanggan
    public function pelanggan(Request $request)
    {
        RolePermission::cek('view:pelanggan');

        // $pelanggan ini yang dipakai di view pelanggan sebagai $pelanggan
        return view('pelanggan', [
            'pelanggan' => $this->pelangganData($request),
        ]);
    }

    //----- pelangganData(): Mengambil data pelanggan dengan filter rentang tanggal (private, dipakai view/pdf/excel)
    private function pelangganData(Request $request)
    {
        return (new database)->tampilTanggal('pelanggan', $request->from, $request->to);
    }

    //----- tplg(): Menampilkan form tambah pelanggan baru
    public function tplg()
    {
        RolePermission::cek('write:pelanggan');

        $data['user'] = (new database)->tampil('users');

        return view('input_pelanggan', $data);
    }

    //----- aksi_tplg(): Menyimpan data pelanggan baru ke database
    public function aksi_tplg(Request $request)
    {
        RolePermission::cek('write:pelanggan');

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:500',
            'email' => 'nullable|email|max:255',
            'no_hp' => 'nullable|string|max:20',
        ]);

        (new database)->tambah('pelanggan', [
            'kode_pelanggan' => 'PLG-' . now()->format('ymdHis'),
            'nama' => $validated['nama'],
            'alamat' => $validated['alamat'],
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'],
        ]);

        return redirect('/pelanggan');
    }

    //----- editPelanggan(): Menampilkan form edit pelanggan berdasarkan id. Mengirim data $row ke view edit_pelanggan
    public function editPelanggan($id)
    {
        RolePermission::cek('write:pelanggan');

        $row = DB::table('pelanggan')->where('id', $id)->first();
        abort_if($row === null, 404);

        // $row ini yang dipakai di view edit_pelanggan sebagai $row
        return view('edit_pelanggan', ['row' => $row]);
    }

    //----- updatePelanggan(): Memperbarui data pelanggan yang dipilih. update() = ubah data di database
    public function updatePelanggan(Request $request, $id)
    {
        RolePermission::cek('write:pelanggan');

        $validated = $request->validate([
            'kode_pelanggan' => 'required|string|max:50',
            'nama' => 'required|string|max:255',
            'no_hp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'alamat' => 'nullable|string|max:500',
        ]);

        DB::table('pelanggan')->where('id', $id)->update([
            'kode_pelanggan' => $validated['kode_pelanggan'],
            'nama' => $validated['nama'],
            'no_hp' => $validated['no_hp'],
            'email' => $validated['email'],
            'alamat' => $validated['alamat'],
            'updated_at' => now(),
        ]);
        return redirect('/pelanggan');
    }

    //----- hapusPelanggan(): Menghapus data pelanggan berdasarkan id. delete() = hapus data dari database
    public function hapusPelanggan($id)
    {
        RolePermission::cek('write:pelanggan');

        DB::table('pelanggan')->where('id', $id)->delete();
        return redirect('/pelanggan');
    }

    //----- pdfPelanggan(): Mengunduh laporan data pelanggan dalam format PDF
    public function pdfPelanggan(Request $request)
    {
        RolePermission::cek('export:pelanggan');

        $export = new PelangganExport($this->pelangganData($request));

        $this->pdfTable('Laporan Data Pelanggan', 'laporan-pelanggan', $export->headings(), $export->collection()->all());
    }

    //----- excelPelanggan(): Mengunduh laporan data pelanggan dalam format Excel (.xlsx)
    public function excelPelanggan(Request $request)
    {
        RolePermission::cek('export:pelanggan');

        return Excel::download(
            new PelangganExport($this->pelangganData($request)),
            'laporan-pelanggan-' . date('Y-m-d') . '.xlsx'
        );
    }
}
