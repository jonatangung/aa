<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\ServisExport;
use Maatwebsite\Excel\Facades\Excel;

//----- ServisController: menangani CRUD transaksi servis beserta export PDF/Excel
class ServisController extends Controller
{
    // ==================== SERVIS ====================

    //----- servis(): Menampilkan halaman daftar servis, data $rows dikirim ke view servis
    public function servis()
    {
        if (!session('u')) return redirect('/');

        //----- $rows ini yang dipakai di view servis sebagai $rows
        return view('servis', ['rows' => $this->servisData()]);
    }

    //----- servisData(): Mengambil data servis lengkap dengan pelanggan, kendaraan, mekanik, daftar jasa, dan metode pembayaran (private)
    private function servisData()
    {
        //----- Ambil data servis, digabung dengan tabel kendaraan, pelanggan, dan mekanik
        $rows = DB::table('servis')
            ->join('kendaraan', 'servis.kendaraan_id', '=', 'kendaraan.id')
            ->join('pelanggan', 'kendaraan.pelanggan_id', '=', 'pelanggan.id')
            ->leftJoin('mekanik', 'servis.mekanik_id', '=', 'mekanik.id')
            ->select(
                'servis.id',
                'servis.no_servis',
                'servis.tanggal_masuk',
                'servis.keluhan',
                'servis.status',
                'pelanggan.nama as nama_pelanggan',
                'kendaraan.no_polisi',
                'mekanik.nama as nama_mekanik'
            )
            ->orderByDesc('servis.id')
            ->get();

        //----- Ambil semua jasa yang dipakai di semua servis
        $jasaSemua = DB::table('servis_jasa')
            ->join('jasa_servis', 'servis_jasa.jasa_servis_id', '=', 'jasa_servis.id')
            ->select('servis_jasa.servis_id', 'jasa_servis.nama_jasa', 'servis_jasa.jumlah', 'servis_jasa.harga')
            ->get();

        //----- Kelompokkan jasa per servis_id supaya gampang dicari
        $jasaByServis = [];
        foreach ($jasaSemua as $jasa) {
            $jasaByServis[$jasa->servis_id][] = $jasa;
        }

        //----- Ambil pembayaran, diurutkan terbaru dulu supaya yang terambil adalah pembayaran terakhir per servis
        $pembayaranSemua = DB::table('pembayaran')
            ->orderByDesc('id')
            ->get();

        $pembayaranByServis = [];
        foreach ($pembayaranSemua as $bayar) {
            //----- Kalau servis ini belum punya pembayaran tersimpan, simpan yang pertama (yang terbaru)
            if (!isset($pembayaranByServis[$bayar->servis_id])) {
                $pembayaranByServis[$bayar->servis_id] = $bayar;
            }
        }

        //----- Tambahkan properti tambahan ke tiap baris untuk dipakai di view dan export
        foreach ($rows as $row) {
            $daftarJasa = [];
            $totalJasa = 0;

            //----- Kumpulkan nama jasa dan hitung total harga
            if (isset($jasaByServis[$row->id])) {
                foreach ($jasaByServis[$row->id] as $jasa) {
                    $daftarJasa[] = $jasa->nama_jasa;
                    $totalJasa = $totalJasa + ($jasa->jumlah * $jasa->harga);
                }
            }

            if (count($daftarJasa) > 0) {
                $row->daftar_jasa = implode(', ', $daftarJasa);
            } else {
                $row->daftar_jasa = '-';
            }
            $row->total_jasa = $totalJasa;

            //----- Ambil metode pembayaran, tampilkan apa adanya (kosong -> '-')
            if (isset($pembayaranByServis[$row->id])) {
                $row->metode_pembayaran = $pembayaranByServis[$row->id]->metode;
            } else {
                $row->metode_pembayaran = '-';
            }
            if ($row->metode_pembayaran == '') {
                $row->metode_pembayaran = '-';
            }
        }

        return $rows;
    }

    //----- tambahServis(): Menampilkan form tambah servis baru (beserta data pelanggan, kendaraan, mekanik, jasa)
    public function tambahServis()
    {
        if (!session('u')) return redirect('/');

        //----- Kirim semua data pilihan ke view input_servis
        return view('input_servis', [
            'pelanggan' => DB::table('pelanggan')->orderBy('nama')->get(),
            'kendaraan' => DB::table('kendaraan')->orderBy('no_polisi')->get(),
            'mekanik' => DB::table('mekanik')->orderBy('nama')->get(),
            'jasa' => DB::table('jasa_servis')->orderBy('nama_jasa')->get(),
        ]);
    }

    //----- simpanServis(): Menyimpan servis baru, detail jasa yang dipilih, dan data pembayarannya
    public function simpanServis(Request $request)
    {
        if (!session('u')) return redirect('/');

        //----- Cari data user yang sedang login berdasarkan nama session
        $user = DB::table('users')->where('nama', session('u'))->first();

        if (!$user) {
            return redirect('/login');
        }

        //----- Buat nomor servis otomatis: SRV001, SRV002, dst.
        $nomorTerakhir = DB::table('servis')
            ->where('no_servis', 'like', 'SRV%')
            ->pluck('no_servis');

        $angkaTerbesar = 0;
        foreach ($nomorTerakhir as $nomor) {
            //----- Buang huruf, sisakan angka saja lalu bandingkan
            $angka = (int) preg_replace('/[^0-9]/', '', $nomor);
            if ($angka > $angkaTerbesar) {
                $angkaTerbesar = $angka;
            }
        }

        //----- Naikkan angka sampai dapat nomor yang belum dipakai
        do {
            $angkaTerbesar++;
            $noServis = 'SRV' . str_pad($angkaTerbesar, 3, '0', STR_PAD_LEFT);
        } while (DB::table('servis')->where('no_servis', $noServis)->exists());

        //----- Mekanik itu opsional, kosong berarti null
        $mekanikId = $request->mekanik_id;
        if ($mekanikId == '') {
            $mekanikId = null;
        }

        //----- Simpan data servis baru, kembalikan id-nya untuk dipakai di tabel lain
        $servisId = DB::table('servis')->insertGetId([
            'no_servis' => $noServis,
            'pelanggan_id' => $request->pelanggan_id,
            'kendaraan_id' => $request->kendaraan_id,
            'mekanik_id' => $mekanikId,
            'user_id' => $user->id,
            'keluhan' => $request->keluhan,
            'status' => $request->status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $jumlahBayar = 0;
        $jasaDipilih = $request->jasa_servis_id;

        //----- Kalau tidak ada jasa yang dicentang, anggap array kosong
        if ($jasaDipilih == null) {
            $jasaDipilih = [];
        }

        //----- Simpan tiap jasa yang dicentang ke tabel servis_jasa
        foreach ($jasaDipilih as $jasaId) {
            $jasa = DB::table('jasa_servis')->where('id', $jasaId)->first();

            if ($jasa) {
                //----- Ambil jumlah jasa dari form, minimal 1
                $jumlah = (int) $request->jumlah_jasa[$jasaId];
                if ($jumlah < 1) {
                    $jumlah = 1;
                }

                DB::table('servis_jasa')->insert([
                    'servis_id' => $servisId,
                    'jasa_servis_id' => $jasa->id,
                    'jumlah' => $jumlah,
                    'harga' => $jasa->harga,
                ]);
                //----- Jumlahkan untuk total pembayaran
                $jumlahBayar = $jumlahBayar + ($jasa->harga * $jumlah);
            }
        }

        //----- Simpan data pembayaran untuk servis ini
        DB::table('pembayaran')->insert([
            'no_pembayaran' => 'PAY-' . $servisId . '-' . time(),
            'servis_id' => $servisId,
            'user_id' => $user->id,
            'tanggal' => now(),
            'metode' => $request->metode_pembayaran,
            'jumlah_bayar' => $jumlahBayar,
            'created_at' => now(),
        ]);

        return redirect('/servis');
    }

    //----- editServis(): Menampilkan form edit servis beserta detail jasa dan pembayaran
    public function editServis($id)
    {
        if (!session('u')) return redirect('/');

        return view('edit_servis', [
            'row' => DB::table('servis')->where('id', $id)->first(),
            'pelanggan' => DB::table('pelanggan')->orderBy('nama')->get(),
            'kendaraan' => DB::table('kendaraan')->orderBy('no_polisi')->get(),
            'mekanik' => DB::table('mekanik')->orderBy('nama')->get(),
            'jasa' => DB::table('jasa_servis')->orderBy('nama_jasa')->get(),
            'detailJasa' => DB::table('servis_jasa')->where('servis_id', $id)->get(),
            'pembayaran' => DB::table('pembayaran')->where('servis_id', $id)->first(),
        ]);
    }

    //----- updateServis(): Memperbarui data servis, menghapus & menyisip ulang detail jasa, lalu update/insert pembayaran
    public function updateServis(Request $request, $id)
    {
        if (!session('u')) return redirect('/');

        //----- Update data utama servis
        DB::table('servis')->where('id', $id)->update([
            'no_servis' => $request->no_servis,
            'pelanggan_id' => $request->pelanggan_id,
            'kendaraan_id' => $request->kendaraan_id,
            'mekanik_id' => $request->mekanik_id,
            'keluhan' => $request->keluhan,
            'status' => $request->status,
            'catatan' => $request->catatan,
            'updated_at' => now(),
        ]);

        //----- Hapus semua detail jasa lama, nanti diisi ulang
        DB::table('servis_jasa')->where('servis_id', $id)->delete();

        $jumlahBayar = 0;
        $jasaDipilih = $request->jasa_servis_id;

        if ($jasaDipilih == null) {
            $jasaDipilih = [];
        }

        //----- Simpan ulang detail jasa yang dipilih
        foreach ($jasaDipilih as $jasaId) {
            $jasa = DB::table('jasa_servis')->where('id', $jasaId)->first();

            if ($jasa) {
                $jumlah = (int) $request->jumlah_jasa[$jasaId];
                if ($jumlah < 1) {
                    $jumlah = 1;
                }

                DB::table('servis_jasa')->insert([
                    'servis_id' => (int) $id,
                    'jasa_servis_id' => (int) $jasa->id,
                    'jumlah' => $jumlah,
                    'harga' => (float) $jasa->harga,
                ]);
                $jumlahBayar = $jumlahBayar + ($jasa->harga * $jumlah);
            }
        }

        //----- Cek apakah servis ini sudah punya data pembayaran
        if (DB::table('pembayaran')->where('servis_id', $id)->exists()) {
            //----- Sudah ada, cukup update
            DB::table('pembayaran')->where('servis_id', $id)->update([
                'metode' => $request->metode_pembayaran,
                'jumlah_bayar' => $jumlahBayar,
            ]);
        } else {
            //----- Belum ada, cari user login lalu buat pembayaran baru
            $user = DB::table('users')->where('nama', session('u'))->first();

            if ($user) {
                DB::table('pembayaran')->insert([
                    'no_pembayaran' => 'PAY-' . $id . '-' . time(),
                    'servis_id' => $id,
                    'user_id' => $user->id,
                    'tanggal' => now(),
                    'metode' => $request->metode_pembayaran,
                    'jumlah_bayar' => $jumlahBayar,
                    'created_at' => now(),
                ]);
            }
        }

        return redirect('/servis');
    }

    //----- hapusServis(): Menghapus servis beserta relasi pembayaran dan servis_jasa-nya
    public function hapusServis($id)
    {
        if (!session('u')) return redirect('/');

        DB::table('pembayaran')->where('servis_id', $id)->delete();
        DB::table('servis_jasa')->where('servis_id', $id)->delete();
        DB::table('servis')->where('id', $id)->delete();
        return redirect('/servis');
    }

    //----- pdfServis(): Mengunduh laporan transaksi servis dalam format PDF
    public function pdfServis()
    {
        if (!session('u')) return redirect('/');

        $export = new ServisExport($this->servisData());

        //----- pdfTable() adalah helper di Controller.php untuk bikin tabel PDF
        $this->pdfTable('Laporan Transaksi Servis', 'laporan-servis', $export->headings(), $export->collection()->all());
    }

    //----- excelServis(): Mengunduh laporan transaksi servis dalam format Excel (.xlsx)
    public function excelServis()
    {
        if (!session('u')) return redirect('/');

        return Excel::download(
            new ServisExport($this->servisData()),
            'laporan-servis-' . date('Y-m-d') . '.xlsx'
        );
    }
}
