@extends('template')

@section('content')
{{-- Halaman laporan pendapatan dengan filter tanggal. Data $rows dan $totalPendapatan dikirim dari LaporanController method laporanPendapatan() --}}

    <style>
        @media print {
            #header, #sidebar, .footer, .no-print {
                display: none !important;
            }
            #main.main {
                margin-left: 0 !important;
                padding: 15px !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
            }
            .dataTable_wrapper > :not(table) {
                display: none !important;
            }
        }
    </style>

    <div class="card shadow-sm border-0 border-top border-warning border-3">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="card-title mb-0">Laporan Pendapatan</h5>
            <div class="d-flex gap-2 flex-wrap no-print">
                <a href="/laporan-pendapatan?from={{ request('from') }}&to={{ request('to') }}&export=pdf" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                <a href="/laporan-pendapatan?from={{ request('from') }}&to={{ request('to') }}&export=excel" class="btn btn-outline-success btn-sm rounded-pill"><i class="bi bi-file-earmark-excel"></i> Excel</a>
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
            </div>
        </div>

        <div class="card-body">
            {{-- Form filter tanggal --}}
            <form class="no-print row g-3 align-items-end mb-3" action="{{ url('/laporan-pendapatan') }}" method="GET">
                <div class="col-md-3 col-sm-6">
                    <label for="from" class="form-label">Dari Tanggal</label>
                    <input type="date" id="from" name="from" value="{{ request('from') }}" class="form-control">
                </div>
                <div class="col-md-3 col-sm-6">
                    <label for="to" class="form-label">Sampai Tanggal</label>
                    <input type="date" id="to" name="to" value="{{ request('to') }}" class="form-control">
                </div>
                <div class="col-md-6 d-flex gap-2">
                    <button type="submit" class="btn btn-warning text-dark fw-semibold rounded-pill"><i class="bi bi-funnel"></i> Filter</button>
                    <a href="/laporan-pendapatan" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
                </div>
            </form>

            {{-- Tabel laporan pendapatan --}}
            <table class="table table-hover table-striped align-middle datatable">
                <thead class="table-warning">
                    <tr>
                        <th>No</th>
                        <th>No Pembayaran</th>
                        <th>No Servis</th>
                        <th>Jasa Dipilih</th>
                        <th>Tanggal</th>
                        <th>Jumlah Bayar</th>
                        <th>Metode</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    {{-- $rows = kumpulan data pembayaran dari controller, $value = satu baris data di setiap putaran --}}
                    @foreach ($rows as $value)
                        <tr>
                            <td>{{ $no }}</td>
                            <?php $no++; ?>
                            <td>{{ $value->no_pembayaran }}</td>
                            <?php if ($value->no_servis == null || $value->no_servis == '') { ?>
                                <td>-</td>
                            <?php } else { ?>
                                <td>{{ $value->no_servis }}</td>
                            <?php } ?>
                            {{-- $value->daftar_jasa = rincian jasa yang sudah dirangkai di controller --}}
                            <td>{{ $value->daftar_jasa }}</td>
                            <td>{{ $value->tanggal }}</td>
                            <td class="fw-bold">Rp{{ number_format($value->total_jasa, 0, ',', '.') }}</td>
                            <td><span class="badge rounded-pill bg-info text-dark">{{ ucfirst($value->metode) }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-warning">
                    {{-- Baris total pendapatan keseluruhan --}}
                    <tr class="fw-bold">
                        <td colspan="5" class="text-end">Total Pendapatan</td>
                        <td class="text-end">Rp{{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

@endsection
