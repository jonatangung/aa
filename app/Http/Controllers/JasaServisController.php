<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\JasaServisExport;
use App\Support\RolePermission;
use Maatwebsite\Excel\Facades\Excel;

//----- JasaServisController: menangani CRUD data jasa servis beserta export PDF/Excel
class JasaServisController extends Controller
{
    // ==================== JASA SERVIS ====================

    //----- jasaServis(): Menampilkan halaman daftar jasa servis. Mengirim data $rows ke view jasa-servis
    public function jasaServis()
    {
        RolePermission::cek('view:jasa_servis');

        // $rows ini yang dipakai di view jasa-servis sebagai $rows
        return view('jasa-servis', ['rows' => $this->jasaServisData()]);
    }

    //----- jasaServisData(): Mengambil seluruh data jasa servis dari database (private). get() = ambil semua data
    private function jasaServisData()
    {
        return DB::table('jasa_servis')->get();
    }

    //----- tambahJasaServis(): Menampilkan form tambah jasa servis baru
    public function tambahJasaServis()
    {
        RolePermission::cek('write:jasa_servis');

        return view('input_jasa_servis');
    }

    //----- simpanJasaServis(): Menyimpan jasa servis baru dengan kode otomatis JS_xxx. insert() = simpan data baru
    public function simpanJasaServis(Request $request)
    {
        RolePermission::cek('write:jasa_servis');

        $validated = $request->validate([
            'nama_jasa' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'estimasi_menit' => 'nullable|integer|min:0',
            'deskripsi' => 'nullable|string|max:1000',
        ]);

        $lastKode = DB::table('jasa_servis')
            ->where('kode_jasa', 'like', 'JS_%')
            ->orderByDesc('kode_jasa')
            ->value('kode_jasa');

        if ($lastKode != '') {
            $angka = (int) substr($lastKode, 3);
            $next = $angka + 1;
        } else {
            $next = 1;
        }

        if ($next < 10) {
            $kodeJasa = 'JS_00' . $next;
        } elseif ($next < 100) {
            $kodeJasa = 'JS_0' . $next;
        } else {
            $kodeJasa = 'JS_' . $next;
        }

        try {
            $tersimpan = DB::table('jasa_servis')->insert([
                'kode_jasa' => $kodeJasa,
                'nama_jasa' => $validated['nama_jasa'],
                'harga' => $validated['harga'],
                'estimasi_menit' => $validated['estimasi_menit'],
                'deskripsi' => $validated['deskripsi'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (!$tersimpan) {
                return redirect('/jasa-servis')->with('gagal', 'Data jasa servis gagal disimpan ke database. Silakan coba lagi.');
            }

            return redirect('/jasa-servis')->with('sukses', 'Data jasa servis berhasil ditambahkan.');
        } catch (\Throwable $e) {
            return redirect('/jasa-servis')->with('gagal', 'Data jasa servis gagal disimpan ke database. Silakan coba lagi.');
        }
    }

    //----- editJasaServis(): Menampilkan form edit jasa servis berdasarkan id. Mengirim data $row ke view edit_jasa_servis
    public function editJasaServis($id)
    {
        RolePermission::cek('write:jasa_servis');

        $row = DB::table('jasa_servis')->where('id', $id)->first();
        abort_if($row === null, 404);

        // $row ini yang dipakai di view edit_jasa_servis sebagai $row
        return view('edit_jasa_servis', ['row' => $row]);
    }

    //----- updateJasaServis(): Memperbarui data jasa servis yang dipilih. update() = ubah data di database
    public function updateJasaServis(Request $request, $id)
    {
        RolePermission::cek('write:jasa_servis');

        $validated = $request->validate([
            'kode_jasa' => 'required|string|max:50',
            'nama_jasa' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'estimasi_menit' => 'nullable|integer|min:0',
            'deskripsi' => 'nullable|string|max:1000',
        ]);

        DB::table('jasa_servis')->where('id', $id)->update([
            'kode_jasa' => $validated['kode_jasa'],
            'nama_jasa' => $validated['nama_jasa'],
            'harga' => $validated['harga'],
            'estimasi_menit' => $validated['estimasi_menit'],
            'deskripsi' => $validated['deskripsi'],
            'updated_at' => now(),
        ]);
        return redirect('/jasa-servis');
    }

    //----- hapusJasaServis(): Menghapus jasa servis beserta relasi servis_jasa-nya. delete() = hapus data dari database
    public function hapusJasaServis($id)
    {
        RolePermission::cek('write:jasa_servis');

        DB::table('servis_jasa')->where('jasa_servis_id', $id)->delete();
        DB::table('jasa_servis')->where('id', $id)->delete();
        return redirect('/jasa-servis');
    }

    //----- pdfJasaServis(): Mengunduh laporan data jasa servis dalam format PDF
    public function pdfJasaServis()
    {
        RolePermission::cek('export:jasa_servis');

        $export = new JasaServisExport($this->jasaServisData());

        $this->pdfTable('Laporan Data Jasa Servis', 'laporan-jasa-servis', $export->headings(), $export->collection()->all());
    }

    //----- excelJasaServis(): Mengunduh laporan data jasa servis dalam format Excel (.xlsx)
    public function excelJasaServis()
    {
        RolePermission::cek('export:jasa_servis');

        return Excel::download(
            new JasaServisExport($this->jasaServisData()),
            'laporan-jasa-servis-' . date('Y-m-d') . '.xlsx'
        );
    }
}
