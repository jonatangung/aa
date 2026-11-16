<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\PembayaranExport;
use Maatwebsite\Excel\Facades\Excel;

//----- PembayaranController: menangani edit/hapus pembayaran beserta export PDF/Excel
class PembayaranController extends Controller
{
    // ==================== PEMBAYARAN ====================

    //----- pembayaran(): Menampilkan halaman daftar pembayaran, data dikirim ke view pembayaran
    public function pembayaran()
    {
        if (!session('u')) return redirect('/');

        return view('pembayaran', $this->pembayaranData());
    }

    //----- pembayaranData(): Mengambil data pembayaran join dengan servis beserta rincian jasa (private)
    private function pembayaranData()
    {
        //----- Ambil semua pembayaran, digabung dengan data servis (no_servis & keluhan)
        $rows = DB::table('pembayaran')
            ->leftJoin('servis', 'servis.id', '=', 'pembayaran.servis_id')
            ->select('pembayaran.*', 'servis.no_servis', 'servis.keluhan')
            ->orderBy('pembayaran.id', 'desc')
            ->get();

        //----- Kumpulkan semua servis_id yang ada supaya bisa dipakai mencari jasa
        $servisIds = [];
        foreach ($rows as $row) {
            if ($row->servis_id != null) {
                $ada = false;
                foreach ($servisIds as $id) {
                    if ($id == $row->servis_id) {
                        $ada = true;
                    }
                }
                if (!$ada) {
                    $servisIds[] = $row->servis_id;
                }
            }
        }

        //----- Ambil detail jasa semua servis sekaligus, dikelompokkan per servis_id
        $jasaByServis = $this->jasaByServis($servisIds);

        //----- Tambahkan rincian jasa dan hitung ulang jumlah bayar ke tiap baris
        foreach ($rows as $row) {
            $daftar = [];
            $total = 0;

            if ($row->servis_id != null) {
                $jasaList = $jasaByServis->get($row->servis_id);

                if ($jasaList != null) {
                    foreach ($jasaList as $jasa) {
                        $daftar[] = $jasa->nama_jasa . ' (' . $jasa->jumlah . 'x) - Rp'
                            . number_format($jasa->jumlah * $jasa->harga, 0, ',', '.');
                        $total = $total + ($jasa->jumlah * $jasa->harga);
                    }
                }
            }

            //----- Simpan rincian jasa jadi satu string untuk dipakai di view dan export
            if (count($daftar) > 0) {
                $row->daftar_jasa = implode(', ', $daftar);
                //----- Kalau ada jasa, jumlah bayar dihitung dari total jasa
                $row->jumlah_bayar = $total;
            } else {
                $row->daftar_jasa = '-';
            }
        }

        return ['rows' => $rows];
    }

    //----- editPembayaran(): Menampilkan form edit pembayaran beserta detail jasa servisnya
    public function editPembayaran($id)
    {
        if (!session('u')) return redirect('/');

        $row = DB::table('pembayaran')->where('id', $id)->first();

        return view('edit_pembayaran', [
            'row' => $row,
            'servis' => DB::table('servis')->orderBy('no_servis')->get(),
            //----- detailJasa = daftar jasa milik servis yang dipilih pembayaran ini
            'detailJasa' => $this->jasaByServis([$row->servis_id])->get($row->servis_id, collect()),
        ]);
    }

    //----- updatePembayaran(): Memperbarui data pembayaran dan menghitung ulang jumlah bayar dari jasa
    public function updatePembayaran(Request $request, $id)
    {
        if (!session('u')) return redirect('/');

        //----- Ambil jasa servis yang dipilih, lalu hitung totalnya
        $jasa = $this->jasaByServis([$request->servis_id])->get($request->servis_id, collect());

        $jumlahBayar = $request->jumlah_bayar;
        if ($jasa->isNotEmpty()) {
            $jumlahBayar = 0;
            foreach ($jasa as $item) {
                $jumlahBayar = $jumlahBayar + ($item->jumlah * $item->harga);
            }
        }

        DB::table('pembayaran')->where('id', $id)->update([
            'no_pembayaran' => $request->no_pembayaran,
            'servis_id' => $request->servis_id,
            'tanggal' => $request->tanggal,
            'metode' => $request->metode,
            'jumlah_bayar' => $jumlahBayar,
            'no_referensi' => $request->no_referensi,
            'catatan' => $request->catatan,
        ]);
        return redirect('/pembayaran');
    }

    //----- hapusPembayaran(): Menghapus data pembayaran berdasarkan id
    public function hapusPembayaran($id)
    {
        if (!session('u')) return redirect('/');

        DB::table('pembayaran')->where('id', $id)->delete();
        return redirect('/pembayaran');
    }

    //----- pdfPembayaran(): Mengunduh laporan pembayaran dalam format PDF
    public function pdfPembayaran()
    {
        if (!session('u')) return redirect('/');

        $export = new PembayaranExport($this->pembayaranData()['rows']);

        $this->pdfTable('Laporan Pembayaran', 'laporan-pembayaran', $export->headings(), $export->collection()->all());
    }

    //----- excelPembayaran(): Mengunduh laporan pembayaran dalam format Excel (.xlsx)
    public function excelPembayaran()
    {
        if (!session('u')) return redirect('/');

        return Excel::download(
            new PembayaranExport($this->pembayaranData()['rows']),
            'laporan-pembayaran-' . date('Y-m-d') . '.xlsx'
        );
    }
}
