@extends('template')

@section('content')
{{-- Halaman laporan servis dengan filter tanggal. Data $rows dikirim dari LaporanController method laporanServis() --}}

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
            <h5 class="card-title mb-0">Laporan Servis</h5>
            <div class="d-flex gap-2 flex-wrap no-print">
                <a href="/laporan-servis?from={{ request('from') }}&to={{ request('to') }}&export=pdf" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                <a href="/laporan-servis?from={{ request('from') }}&to={{ request('to') }}&export=excel" class="btn btn-outline-success btn-sm rounded-pill"><i class="bi bi-file-earmark-excel"></i> Excel</a>
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
            </div>
        </div>

        <div class="card-body">
            {{-- Form filter tanggal --}}
            <form class="no-print row g-3 align-items-end mb-3" action="{{ url('/laporan-servis') }}" method="GET">
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
                    <a href="/laporan-servis" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
                </div>
            </form>

            {{-- Tabel laporan servis --}}
            <table class="table table-hover table-striped align-middle datatable">
                <thead class="table-warning">
                    <tr>
                        <th>No</th>
                        <th>No Servis</th>
                        <th>Pelanggan</th>
                        <th>No Polisi</th>
                        <th>Mekanik</th>
                        <th>Tanggal Masuk</th>
                        <th>Keluhan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php
                    //----- Warna badge untuk status servis
                    $statusBadge = [
                        'antri' => 'bg-warning',
                        'dikerjakan' => 'bg-info',
                        'selesai' => 'bg-success',
                        'diambil' => 'bg-primary',
                        'batal' => 'bg-danger',
                    ];
                    ?>
                    {{-- $rows = kumpulan data laporan dari controller, $value = satu baris data di setiap putaran --}}
                    @foreach ($rows as $value)
                        <tr>
                            <td>{{ $no }}</td>
                            <?php $no++; ?>
                            <td>{{ $value->no_servis }}</td>
                            <td>{{ $value->nama_pelanggan }}</td>
                            <td>{{ $value->no_polisi }}</td>
                            <?php if ($value->nama_mekanik == null || $value->nama_mekanik == '') { ?>
                                <td>-</td>
                            <?php } else { ?>
                                <td>{{ $value->nama_mekanik }}</td>
                            <?php } ?>
                            <td>{{ $value->tanggal_masuk }}</td>
                            <?php if ($value->keluhan == null || $value->keluhan == '') { ?>
                                <td>-</td>
                            <?php } else { ?>
                                <td>{{ $value->keluhan }}</td>
                            <?php } ?>
                            <td><span class="badge rounded-pill {{ $statusBadge[$value->status] ?? 'bg-secondary' }}">{{ ucfirst($value->status) }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
