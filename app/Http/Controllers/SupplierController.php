<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\SupplierExport;
use App\Support\RolePermission;
use Maatwebsite\Excel\Facades\Excel;

//----- SupplierController: menangani CRUD data supplier/barang beserta export PDF/Excel
class SupplierController extends Controller
{
    // ==================== SUPPLIER ====================

    //----- supplier(): Menampilkan halaman daftar supplier/barang. Mengirim data $rows ke view supplier
    public function supplier()
    {
        RolePermission::cek('view:supplier');

        // $rows ini yang dipakai di view supplier sebagai $rows
        return view('supplier', ['rows' => $this->supplierData()]);
    }

    //----- supplierData(): Mengambil seluruh data supplier/barang dari database (private). get() = ambil semua data
    private function supplierData()
    {
        return DB::table('supplier')->get();
    }

    //----- tambahSupplier(): Menampilkan form tambah supplier/barang baru
    public function tambahSupplier()
    {
        RolePermission::cek('write:supplier');

        return view('input_supplier');
    }

    //----- simpanSupplier(): Menyimpan supplier baru beserta catatan stok masuk awal jika ada. insertGetId() = simpan data dan ambil id baru
    public function simpanSupplier(Request $request)
    {
        RolePermission::cek('write:supplier');

        $validated = $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'harga' => 'nullable|numeric|min:0',
            'stok' => 'nullable|integer|min:0',
            'tanggal_masuk' => 'nullable|date',
            'kontak_person' => 'nullable|string|max:255',
            'no_hp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'alamat' => 'nullable|string|max:500',
        ]);

        $harga = $validated['harga'] ?? 0;
        $tanggalMasuk = $validated['tanggal_masuk'] ?? now()->toDateString();

        $supplierId = DB::table('supplier')->insertGetId([
            'nama_supplier' => $validated['nama_supplier'],
            'nama_barang' => $validated['nama_barang'],
            'stok' => 0,
            'harga' => $harga,
            'tanggal_masuk' => $tanggalMasuk,
            'kontak_person' => $validated['kontak_person'],
            'no_hp' => $validated['no_hp'],
            'email' => $validated['email'],
            'alamat' => $validated['alamat'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ((int) ($validated['stok'] ?? 0) > 0) {
            $tanggalStok = $validated['tanggal_masuk'] ?? now()->toDateString();
            DB::table('stok_barang')->insert([
                'supplier_id' => $supplierId,
                'jenis' => 'masuk',
                'jumlah' => $validated['stok'],
                'tanggal' => $tanggalStok,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect('/supplier');
    }

    //----- editSupplier(): Menampilkan form edit supplier/barang berdasarkan id. Mengirim data $row ke view edit_supplier
    public function editSupplier($id)
    {
        RolePermission::cek('write:supplier');

        $row = DB::table('supplier')->where('id', $id)->first();
        abort_if($row === null, 404);

        // $row ini yang dipakai di view edit_supplier sebagai $row
        return view('edit_supplier', ['row' => $row]);
    }

    //----- updateSupplier(): Memperbarui data supplier dan mencatat selisih stok (masuk/keluar). update() = ubah data di database
    public function updateSupplier(Request $request, $id)
    {
        RolePermission::cek('write:supplier');

        $validated = $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'harga' => 'nullable|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'tanggal_masuk' => 'nullable|date',
            'kontak_person' => 'nullable|string|max:255',
            'no_hp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'alamat' => 'nullable|string|max:500',
        ]);

        $supplier = DB::table('supplier')->where('id', $id)->first();
        abort_if($supplier === null, 404);

        $stokLama = (int) $supplier->stok;
        $stokBaru = (int) $validated['stok'];
        $harga = $validated['harga'] ?? 0;

        DB::table('supplier')->where('id', $id)->update([
            'nama_supplier' => $validated['nama_supplier'],
            'nama_barang' => $validated['nama_barang'],
            'harga' => $harga,
            'tanggal_masuk' => $validated['tanggal_masuk'],
            'kontak_person' => $validated['kontak_person'],
            'no_hp' => $validated['no_hp'],
            'email' => $validated['email'],
            'alamat' => $validated['alamat'],
            'updated_at' => now(),
        ]);

        if ($stokBaru > $stokLama) {
            $selisih = $stokBaru - $stokLama;
            $tanggalStok = $validated['tanggal_masuk'] ?? now()->toDateString();
            DB::table('stok_barang')->insert([
                'supplier_id' => $id,
                'jenis' => 'masuk',
                'jumlah' => $selisih,
                'tanggal' => $tanggalStok,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } elseif ($stokBaru < $stokLama) {
            $selisih = $stokLama - $stokBaru;
            DB::table('stok_barang')->insert([
                'supplier_id' => $id,
                'jenis' => 'keluar',
                'jumlah' => $selisih,
                'tanggal' => $validated['tanggal_masuk'] ?? now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect('/supplier');
    }

    //----- hapusSupplier(): Menghapus data supplier/barang berdasarkan id. delete() = hapus data dari database
    public function hapusSupplier($id)
    {
        RolePermission::cek('write:supplier');

        DB::table('supplier')->where('id', $id)->delete();
        return redirect('/supplier');
    }

    //----- pdfSupplier(): Mengunduh laporan data supplier dalam format PDF
    public function pdfSupplier()
    {
        RolePermission::cek('export:supplier');

        $export = new SupplierExport($this->supplierData());

        $this->pdfTable('Laporan Data Supplier', 'laporan-supplier', $export->headings(), $export->collection()->all());
    }

    //----- excelSupplier(): Mengunduh laporan data supplier dalam format Excel (.xlsx)
    public function excelSupplier()
    {
        RolePermission::cek('export:supplier');

        return Excel::download(
            new SupplierExport($this->supplierData()),
            'laporan-supplier-' . date('Y-m-d') . '.xlsx'
        );
    }
}
