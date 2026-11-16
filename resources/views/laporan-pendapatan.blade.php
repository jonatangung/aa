@extends('template')

@section('content')
{{-- Halaman laporan pendapatan dengan filter tanggal. Data $rows dan $totalPendapatan dikirim dari LaporanController method laporanPendapatan() --}}

    <style>
        @media print {
            #header, #sidebar, .footer, .no-print, .page-title {
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
            .table-responsive {
                overflow: visible !important;
            }
        }
    </style>

    <h1 class="mb-3 page-title">Laporan Pendapatan</h1>

    {{-- Form filter tanggal dan tombol export --}}
    <form class="no-print d-flex flex-wrap align-items-center gap-3 mb-4" action="{{ url('/laporan-pendapatan') }}" method="GET">
        <label for="from" class="fw-semibold text-nowrap">Dari Tanggal:</label>
        <input type="date" id="from" name="from" value="{{ request('from') }}" class="form-control w-auto">

        <label for="to" class="fw-semibold text-nowrap">Sampai Tanggal:</label>
        <input type="date" id="to" name="to" value="{{ request('to') }}" class="form-control w-auto">

        <button type="submit" class="btn btn-secondary">Filter</button>
        <a href="/laporan-pendapatan" class="btn btn-secondary">Reset</a>
        <button type="button" class="btn btn-secondary" onclick="window.print()">Print</button>
        <a href="/laporan-pendapatan?from={{ request('from') }}&to={{ request('to') }}&export=pdf" class="btn btn-danger">PDF</a>
        <a href="/laporan-pendapatan?from={{ request('from') }}&to={{ request('to') }}&export=excel" class="btn btn-success">Excel</a>
    </form>

    {{-- Tabel laporan pendapatan --}}
    <div class="card mt-3">
        <div class="card-body table-responsive">
            <h5 class="card-title">Laporan Pendapatan</h5>

            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark text-center">
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
                            <td>{{ $value->metode }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    {{-- Baris total pendapatan keseluruhan --}}
                    <tr class="table-dark fw-bold">
                        <td colspan="5" class="text-end">Total Pendapatan</td>
                        <td class="text-end">Rp{{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

@endsection
