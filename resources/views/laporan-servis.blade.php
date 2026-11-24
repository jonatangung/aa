@extends('template')

@section('content')
{{-- Halaman laporan servis dengan filter tanggal (role lain) atau kode servis+no polisi (tamu). Data $rows dikirim dari LaporanController method laporanServis() --}}

    <?php $canExport = \App\Support\RolePermission::boleh('export:laporan_servis'); ?>

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
            /* Paksa teks biru tua agar terbaca jelas saat dicetak */
            #main.main,
            #main.main .card,
            #main.main .card-title,
            #main.main .table,
            #main.main .table thead th,
            #main.main .table tbody td,
            #main.main .table tfoot td,
            #main.main .badge {
                color: #1d4ed8 !important;
            }
            #main.main .table thead th {
                background-color: #dbeafe !important;
                text-shadow: none !important;
            }
            #main.main .badge {
                background-color: #dbeafe !important;
                border-color: #93c5fd !important;
            }
        }
    </style>

    <div class="card shadow-sm border-0 border-top border-warning border-3">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="card-title mb-0">Laporan Servis</h5>
            <div class="d-flex gap-2 flex-wrap no-print">
                <?php if (!$isTamu && $canExport) { ?>
                <a href="/laporan-servis?from={{ request('from') }}&to={{ request('to') }}&export=pdf" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                <a href="/laporan-servis?from={{ request('from') }}&to={{ request('to') }}&export=excel" target="_blank" class="btn btn-outline-success btn-sm rounded-pill"><i class="bi bi-file-earmark-excel"></i> Excel</a>
                <?php } ?>
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
            </div>
        </div>

        <div class="card-body">
            {{-- Form filter berbeda untuk tamu vs role lain --}}
            <?php if ($isTamu) { ?>
                {{-- Form filter untuk tamu: kode servis + no polisi --}}
                <form class="no-print row g-3 align-items-end mb-3" action="{{ url('/laporan-servis') }}" method="GET">
                    <div class="col-md-5 col-sm-6">
                        <label for="no_servis" class="form-label fw-semibold">Kode Servis</label>
                        <input type="text" id="no_servis" name="no_servis" value="{{ request('no_servis') }}" class="form-control" placeholder="Contoh: SRV001">
                    </div>
                    <div class="col-md-5 col-sm-6">
                        <label for="no_polisi" class="form-label fw-semibold">Nomor Polisi</label>
                        <input type="text" id="no_polisi" name="no_polisi" value="{{ request('no_polisi') }}" class="form-control" placeholder="Contoh: B 1234 ABC">
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-warning text-white fw-semibold rounded-pill"><i class="bi bi-search"></i> Cari</button>
                        <a href="/laporan-servis" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
                    </div>
                </form>
            <?php } else { ?>
                {{-- Form filter tanggal untuk role lain --}}
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
                        <button type="submit" class="btn btn-warning text-white fw-semibold rounded-pill"><i class="bi bi-funnel"></i> Filter</button>
                        <a href="/laporan-servis" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
                    </div>
                </form>
            <?php } ?>

            {{-- Tabel laporan servis --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th>No Servis</th>
                            <th>Pelanggan</th>
                            <th>No Polisi</th>
                            <th>Mekanik</th>
                            <th>Tanggal Masuk</th>
                            <th>Keluhan</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php
                        //----- Warna badge untuk status servis
                        $statusBadge = [
                            'antri' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                            'dikerjakan' => 'bg-info-subtle text-info-emphasis border border-info-subtle',
                            'selesai' => 'bg-success-subtle text-success-emphasis border border-success-subtle',
                            'diambil' => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle',
                            'batal' => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
                        ];
                        ?>
                        {{-- $rows = kumpulan data laporan dari controller, $value = satu baris data di setiap putaran --}}
                        @foreach ($rows as $value)
                            <tr>
                                <td class="text-center">{{ $no }}</td>
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
                                <td class="text-center"><span class="badge rounded-pill px-3 py-2 {{ $statusBadge[$value->status] ?? 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle' }}">{{ ucfirst($value->status) }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
