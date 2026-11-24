<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use App\Exports\StokBarangExport;
use App\Support\RolePermission;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

//----- StokBarangController: menangani rekap dan transaksi stok barang (masuk/keluar) beserta export PDF/Excel
class StokBarangController extends Controller
{
    // ==================== STOK BARANG ====================

    //----- stokBarang(): Menampilkan halaman rekap stok barang
    public function stokBarang(Request $request)
    {
        RolePermission::cek('view:stok_barang');

        //----- $supplierData dipakai untuk dropdown pilih barang di modal kelola stok
        return view('stok-barang', [
            'rows' => $this->stokBarangData(),
            'supplierData' => DB::table('supplier')->get(),
        ]);
    }

    //----- stokBarangData(): Query rekap total stok masuk/keluar per barang beserta tanggal terakhir (private)
    private function stokBarangData()
    {
        return DB::select("
            SELECT supplier.id, supplier.nama_supplier, supplier.nama_barang,
            supplier.harga, supplier.stok, supplier.harga_jual,
            COALESCE(SUM(CASE WHEN stok_barang.jenis = 'masuk' THEN stok_barang.jumlah ELSE 0 END), 0) AS stok_masuk,
            COALESCE(MAX(CASE WHEN stok_barang.jenis = 'masuk' THEN stok_barang.tanggal END), '-') AS tanggal_masuk,
            COALESCE(SUM(CASE WHEN stok_barang.jenis = 'keluar' THEN stok_barang.jumlah ELSE 0 END), 0) AS stok_keluar,
            COALESCE(MAX(CASE WHEN stok_barang.jenis = 'keluar' THEN stok_barang.tanggal END), '-') AS tanggal_keluar
            FROM supplier
            LEFT JOIN stok_barang ON stok_barang.supplier_id = supplier.id
            GROUP BY supplier.id, supplier.nama_supplier, supplier.nama_barang,
            supplier.harga, supplier.stok, supplier.harga_jual
            ORDER BY supplier.id
        ");
    }

    //----- inputStokBarang(): Menampilkan form kelola stok dengan pilihan barang dari supplier
    public function inputStokBarang(Request $request)
    {
        RolePermission::cek('write:stok_barang');

        //----- Cari barang yang dipilih lewat dropdown (kosong = belum pilih)
        $selectedBarang = null;
        if ($request->supplier_id) {
            $selectedBarang = DB::table('supplier')
                ->where('id', $request->supplier_id)
                ->first();
        }

        return view('input_stok_barang', [
            'supplierData' => DB::table('supplier')->get(),
            'selectedBarang' => $selectedBarang,
        ]);
    }

    //----- stokMasuk(): Mencatat transaksi stok masuk dan memperbarui tanggal_masuk supplier
    public function stokMasuk(Request $request)
    {
        RolePermission::cek('write:stok_barang');

        $validated = $request->validate([
            'supplier_id' => 'required|integer|exists:supplier,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal' => 'nullable|date',
        ]);

        //----- Tanggal kosong berarti pakai tanggal hari ini
        $tanggal = $validated['tanggal'] ?? now()->toDateString();

        try {
            DB::table('stok_barang')->insert([
                'supplier_id' => $validated['supplier_id'],
                'jenis' => 'masuk',
                'jumlah' => $validated['jumlah'],
                'tanggal' => $tanggal,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('supplier')->where('id', $validated['supplier_id'])->update([
                'tanggal_masuk' => $tanggal,
                'updated_at' => now(),
            ]);

            return redirect('/stok-barang')->with('sukses', 'Stok masuk berhasil dicatat.');
        } catch (\Throwable $e) {
            return redirect('/stok-barang')->with('gagal', 'Stok masuk gagal disimpan ke database. Silakan coba lagi.');
        }
    }

    //----- stokKeluar(): Mencatat transaksi stok keluar dan memperbarui harga_jual barang
    public function stokKeluar(Request $request, $id)
    {
        RolePermission::cek('write:stok_barang');

        $validated = $request->validate([
            'jumlah' => 'required|integer|min:1',
            'tanggal' => 'nullable|date',
            'harga_jual' => 'nullable|numeric|min:0',
        ]);

        $supplier = DB::table('supplier')->where('id', $id)->first();
        abort_if($supplier === null, 404);

        $jumlah = (int) $validated['jumlah'];

        //----- Harga jual dan tanggal kosong diisi nilai default
        $hargaJual = $validated['harga_jual'] ?? 0;
        $tanggal = $validated['tanggal'] ?? now()->toDateString();

        try {
            if ($jumlah > 0) {
                DB::table('supplier')->where('id', $id)->update([
                    'harga_jual' => $hargaJual,
                    'updated_at' => now(),
                ]);

                DB::table('stok_barang')->insert([
                    'supplier_id' => $id,
                    'jenis' => 'keluar',
                    'jumlah' => $jumlah,
                    'tanggal' => $tanggal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return redirect('/stok-barang')->with('sukses', 'Stok keluar berhasil dicatat.');
        } catch (\Throwable $e) {
            return redirect('/stok-barang')->with('gagal', 'Stok keluar gagal disimpan ke database. Silakan coba lagi.');
        }
    }

    //----- pdfStokBarang(): Mengunduh laporan stok barang dalam format PDF
    public function pdfStokBarang()
    {
        RolePermission::cek('export:stok_barang');

        $export = new StokBarangExport($this->stokBarangData());

        $this->pdfTable('Laporan Stok Barang', 'laporan-stok-barang', $export->headings(), $export->collection()->all());
    }

    //----- excelStokBarang(): Mengunduh laporan stok barang dalam format Excel (.xlsx)
    public function excelStokBarang()
    {
        RolePermission::cek('export:stok_barang');

        return Excel::download(
            new StokBarangExport($this->stokBarangData()),
            'laporan-stok-barang-' . date('Y-m-d') . '.xlsx'
        );
    }

    //----- generateQRCode(): Generate QR Code sementara untuk halaman Data Masuk (berlaku 5 menit)
    public function generateQRCode()
    {
        RolePermission::cek('write:stok_barang');

        // Generate signed URL yang berlaku 5 menit
        $signedUrl = URL::temporarySignedRoute(
            'data-masuk.form',
            now()->addMinutes(5)
        );

        // Generate QR Code dari signed URL sebagai string SVG
        $qrCodeSvg = (string) QrCode::size(300)->generate($signedUrl);

        return response()->json([
            'qr_code' => $qrCodeSvg,
            'type' => 'svg',
            'expires_in_seconds' => 300, // 5 menit = 300 detik
        ]);
    }

    //----- dataMasukForm(): Halaman publik Data Masuk (hanya bisa diakses via signed URL QR Code)
    public function dataMasukForm()
    {
        // Mendapatkan daftar barang dari tabel supplier
        $barangList = DB::table('supplier')
            ->select('id', 'nama_barang', 'nama_supplier')
            ->whereNotNull('nama_barang')
            ->where('nama_barang', '!=', '')
            ->orderBy('nama_barang')
            ->get();

        return view('data-masuk', [
            'barangList' => $barangList,
            'today' => now()->toDateString(),
        ]);
    }

    //----- simpanDataMasuk(): Menyimpan data masuk dari form publik
    public function simpanDataMasuk(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|integer|exists:supplier,id',
            'stok_masuk' => 'required|integer|min:1',
            'harga' => 'required|numeric|min:0',
            'tanggal_masuk' => 'required|date',
        ]);

        $supplierId = $validated['supplier_id'];
        $stokMasuk = $validated['stok_masuk'];
        $harga = $validated['harga'];
        $tanggalMasuk = $validated['tanggal_masuk'];

        // Insert ke tabel stok_barang
        DB::table('stok_barang')->insert([
            'supplier_id' => $supplierId,
            'jenis' => 'masuk',
            'jumlah' => $stokMasuk,
            'tanggal' => $tanggalMasuk,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update tabel supplier (harga ditambahkan, tanggal_masuk diupdate, stok otomatis diupdate oleh trigger)
        DB::table('supplier')->where('id', $supplierId)->update([
            'harga' => DB::raw('harga + ' . $harga),
            'tanggal_masuk' => $tanggalMasuk,
            'updated_at' => now(),
        ]);

        return view('data-masuk-success');
    }
}
