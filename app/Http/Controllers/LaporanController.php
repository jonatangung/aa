<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\LaporanServisExport;
use App\Exports\LaporanPendapatanExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Support\RolePermission;

//----- LaporanController: menangani laporan servis & pendapatan dengan filter tanggal dan export PDF/Excel
class LaporanController extends Controller
{
    //---------------------------- LAPORAN ----------------------------

    //----- laporanServis(): Menampilkan laporan servis dengan filter tanggal; mendukung export PDF/Excel via parameter export
    public function laporanServis(Request $request)
    {
        RolePermission::cek('view:laporan_servis');

        $validated = $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'export' => 'nullable|in:pdf,excel',
        ]);

        //----- $rows = hasil query laporan servis dengan filter tanggal
        $rows = $this->laporanServisData($request);

        //----- Export hanya boleh untuk owner & admin
        if ($request->export != null && $request->export != '') {
            if (!in_array(session('role'), ['owner', 'admin'])) {
                return redirect('/laporan-servis');
            }

            if ($request->export == 'pdf') {
                return $this->downloadPdf('pdf.laporan-servis', 'Laporan_Servis', [
                    'rows' => $rows,
                    'from' => $request->from,
                    'to' => $request->to,
                ]);
            }

            if ($request->export == 'excel') {
                return Excel::download(new LaporanServisExport($rows), 'Laporan_Servis.xlsx');
            }
        }

        return view('laporan-servis', ['rows' => $rows]);
    }

    //----- laporanServisData(): Query data servis join kendaraan, pelanggan, mekanik dengan filter tanggal (private)
    private function laporanServisData(Request $request)
    {
        $query = DB::table('servis')
            ->join('kendaraan', 'servis.kendaraan_id', '=', 'kendaraan.id')
            ->join('pelanggan', 'kendaraan.pelanggan_id', '=', 'pelanggan.id')
            ->leftJoin('mekanik', 'servis.mekanik_id', '=', 'mekanik.id')
            ->select(
                'servis.no_servis',
                'pelanggan.nama as nama_pelanggan',
                'kendaraan.no_polisi',
                'mekanik.nama as nama_mekanik',
                'servis.tanggal_masuk',
                'servis.keluhan',
                'servis.status'
            );

        //----- Filter tanggal mulai kalau diisi
        if ($request->from != null && $request->from != '') {
            $query->whereDate('servis.tanggal_masuk', '>=', $request->from);
        }

        //----- Filter tanggal akhir kalau diisi
        if ($request->to != null && $request->to != '') {
            $query->whereDate('servis.tanggal_masuk', '<=', $request->to);
        }

        return $query->orderByDesc('servis.tanggal_masuk')->get();
    }

    //----- laporanPendapatan(): Menampilkan laporan pendapatan dengan filter tanggal; mendukung export PDF/Excel
    public function laporanPendapatan(Request $request)
    {
        RolePermission::cek('view:laporan_pendapatan');

        $validated = $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'export' => 'nullable|in:pdf,excel',
        ]);

        //----- $data berisi rows, jasaByServis, dan totalPendapatan dari method laporanPendapatanData
        $data = $this->laporanPendapatanData($request);

        //----- Export hanya boleh untuk owner & admin
        if ($request->export != null && $request->export != '') {
            if (!in_array(session('role'), ['owner', 'admin'])) {
                return redirect('/laporan-pendapatan');
            }

            if ($request->export == 'pdf') {
                return $this->downloadPdf('pdf.laporan-pendapatan', 'Laporan_Pendapatan', [
                    'rows' => $data['rows'],
                    'totalPendapatan' => $data['totalPendapatan'],
                    'from' => $request->from,
                    'to' => $request->to,
                ]);
            }

            if ($request->export == 'excel') {
                return Excel::download(
                    new LaporanPendapatanExport($data['rows'], $data['totalPendapatan']),
                    'Laporan_Pendapatan.xlsx'
                );
            }
        }

        return view('laporan-pendapatan', $data);
    }

    //----- laporanPendapatanData(): Mengambil data pembayaran join servis, hitung rincian jasa dan total pendapatan (private)
    private function laporanPendapatanData(Request $request)
    {
        $query = DB::table('pembayaran')
            ->leftJoin('servis', 'servis.id', '=', 'pembayaran.servis_id')
            ->select('pembayaran.*', 'servis.no_servis', 'servis.keluhan');

        if ($request->from != null && $request->from != '') {
            $query->whereDate('pembayaran.tanggal', '>=', $request->from);
        }

        if ($request->to != null && $request->to != '') {
            $query->whereDate('pembayaran.tanggal', '<=', $request->to);
        }

        $rows = $query->orderByDesc('pembayaran.tanggal')->get();

        //----- Kumpulkan servis_id yang ada untuk mencari jasa
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

        //----- Detail jasa semua servis, dikelompokkan per servis_id
        $jasaByServis = $this->jasaByServis($servisIds);

        $totalPendapatan = 0;

        //----- Tambahkan rincian jasa dan total bayar ke tiap baris
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

            if (count($daftar) > 0) {
                $row->daftar_jasa = implode(', ', $daftar);
                $row->total_jasa = $total;
            } else {
                $row->daftar_jasa = '-';
                //----- Tidak ada jasa, pakai jumlah bayar yang tersimpan
                $row->total_jasa = $row->jumlah_bayar;
            }

            $totalPendapatan = $totalPendapatan + $row->total_jasa;
        }

        return [
            'rows' => $rows,
            'totalPendapatan' => $totalPendapatan,
        ];
    }
}
