<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\KendaraanExport;
use App\Support\RolePermission;
use Maatwebsite\Excel\Facades\Excel;

//----- KendaraanController: menangani CRUD data kendaraan beserta export PDF/Excel
class KendaraanController extends Controller
{
    // ==================== KENDARAAN ====================

    //----- kendaraan(): Menampilkan halaman daftar kendaraan. Mengirim data $rows ke view kendaraan
    public function kendaraan()
    {
        RolePermission::cek('view:kendaraan');

        // $rows ini yang dipakai di view kendaraan sebagai $rows
        // $pelanggan dipakai untuk dropdown di modal tambah kendaraan
        return view('kendaraan', [
            'rows' => $this->kendaraanData(),
            'pelanggan' => DB::table('pelanggan')
                ->select('id', 'nama', 'kode_pelanggan')
                ->orderBy('nama')
                ->get(),
        ]);
    }

    //----- kendaraanData(): Mengambil seluruh data kendaraan dari database (private). join() = gabung tabel, select() = pilih kolom, orderBy() = urutkan data, get() = ambil semua data
    private function kendaraanData()
    {
        return DB::table('kendaraan')
            ->join('pelanggan', 'kendaraan.pelanggan_id', '=', 'pelanggan.id')
            ->select('kendaraan.*', 'pelanggan.nama as nama_pelanggan')
            ->orderBy('kendaraan.no_polisi')
            ->get();
    }

    //----- tambahKendaraan(): Menampilkan form tambah kendaraan baru (beserta daftar pelanggan). Mengirim data $pelanggan ke view input_kendaraan
    public function tambahKendaraan()
    {
        RolePermission::cek('write:kendaraan');

        // $pelanggan ini yang dipakai di view input_kendaraan sebagai $pelanggan
        return view('input_kendaraan', [
            'pelanggan' => DB::table('pelanggan')
                ->select('id', 'nama', 'kode_pelanggan')
                ->orderBy('nama')
                ->get(),
        ]);
    }

    //----- simpanKendaraan(): Menyimpan data kendaraan baru ke database
    public function simpanKendaraan(Request $request)
    {
        RolePermission::cek('write:kendaraan');

        $validated = $request->validate([
            'pelanggan_id' => 'required|integer|exists:pelanggan,id',
            'no_polisi' => 'required|string|max:20',
            'merk' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'tahun' => 'nullable|integer|min:1900|max:2100',
            'warna' => 'nullable|string|max:50',
            'no_rangka' => 'nullable|string|max:100',
            'no_mesin' => 'nullable|string|max:100',
        ]);

        try {
            $tersimpan = DB::table('kendaraan')->insert([
                'pelanggan_id' => $validated['pelanggan_id'],
                'no_polisi' => $validated['no_polisi'],
                'merk' => $validated['merk'],
                'model' => $validated['model'],
                'tahun' => $validated['tahun'],
                'warna' => $validated['warna'],
                'no_rangka' => $validated['no_rangka'],
                'no_mesin' => $validated['no_mesin'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (!$tersimpan) {
                return redirect('/kendaraan')->with('gagal', 'Data kendaraan gagal disimpan ke database. Silakan coba lagi.');
            }

            return redirect('/kendaraan')->with('sukses', 'Data kendaraan berhasil ditambahkan.');
        } catch (\Throwable $e) {
            return redirect('/kendaraan')->with('gagal', 'Data kendaraan gagal disimpan ke database. Silakan coba lagi.');
        }
    }

    //----- editKendaraan(): Menampilkan form edit kendaraan berdasarkan id. Mengirim data $row dan $pelanggan ke view edit_kendaraan
    public function editKendaraan($id)
    {
        RolePermission::cek('write:kendaraan');

        $row = DB::table('kendaraan')->where('id', $id)->first();
        abort_if($row === null, 404);

        // $row dan $pelanggan ini yang dipakai di view edit_kendaraan
        return view('edit_kendaraan', [
            'row' => $row,
            'pelanggan' => DB::table('pelanggan')->orderBy('nama')->get(),
        ]);
    }

    //----- updateKendaraan(): Memperbarui data kendaraan yang dipilih. update() = ubah data di database
    public function updateKendaraan(Request $request, $id)
    {
        RolePermission::cek('write:kendaraan');

        $validated = $request->validate([
            'pelanggan_id' => 'required|integer|exists:pelanggan,id',
            'no_polisi' => 'required|string|max:20',
            'merk' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'tahun' => 'nullable|integer|min:1900|max:2100',
            'warna' => 'nullable|string|max:50',
            'no_rangka' => 'nullable|string|max:100',
            'no_mesin' => 'nullable|string|max:100',
        ]);

        DB::table('kendaraan')->where('id', $id)->update([
            'pelanggan_id' => $validated['pelanggan_id'],
            'no_polisi' => $validated['no_polisi'],
            'merk' => $validated['merk'],
            'model' => $validated['model'],
            'tahun' => $validated['tahun'],
            'warna' => $validated['warna'],
            'no_rangka' => $validated['no_rangka'],
            'no_mesin' => $validated['no_mesin'],
            'updated_at' => now(),
        ]);
        return redirect('/kendaraan');
    }

    //----- hapusKendaraan(): Menghapus data kendaraan berdasarkan id. delete() = hapus data dari database
    public function hapusKendaraan($id)
    {
        RolePermission::cek('write:kendaraan');

        DB::table('kendaraan')->where('id', $id)->delete();
        return redirect('/kendaraan');
    }

    //----- pdfKendaraan(): Mengunduh laporan data kendaraan dalam format PDF
    public function pdfKendaraan()
    {
        RolePermission::cek('export:kendaraan');

        $export = new KendaraanExport($this->kendaraanData());

        $this->pdfTable('Laporan Data Kendaraan', 'laporan-kendaraan', $export->headings(), $export->collection()->all());
    }

    //----- excelKendaraan(): Mengunduh laporan data kendaraan dalam format Excel (.xlsx)
    public function excelKendaraan()
    {
        RolePermission::cek('export:kendaraan');

        return Excel::download(
            new KendaraanExport($this->kendaraanData()),
            'laporan-kendaraan-' . date('Y-m-d') . '.xlsx'
        );
    }
}
