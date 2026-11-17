@extends('template')

@section('content')
{{-- Halaman daftar transaksi servis. Data $rows dikirim dari ServisController method servis() --}}

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

    <?php $canWrite = \App\Support\RolePermission::boleh('write:servis'); ?>
    <?php $canExport = \App\Support\RolePermission::boleh('export:servis'); ?>

    <div class="card shadow-sm border-0 border-top border-warning border-3">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="card-title mb-0">Transaksi Servis</h5>
            <div class="d-flex gap-2 flex-wrap no-print">
                <?php if ($canWrite) { ?>
                <a href="/tambah_servis" class="btn btn-warning text-dark fw-semibold btn-sm rounded-pill"><i class="bi bi-plus-lg-circle"></i> Tambah Servis</a>
                <?php } ?>
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
                <?php if ($canExport) { ?>
                <a href="/servis/pdf" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                <a href="/servis/excel" class="btn btn-outline-success btn-sm rounded-pill"><i class="bi bi-file-earmark-excel"></i> Excel</a>
                <?php } ?>
            </div>
        </div>

        <div class="card-body">
            {{-- Tabel daftar transaksi servis --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th>Pelanggan</th>
                            <th>Kendaraan</th>
                            <th>Jasa Servis</th>
                            <th class="text-end">Harga</th>
                            <th>Mekanik</th>
                            <th>Metode Pembayaran</th>
                            <th>Tanggal</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        {{-- $rows = kumpulan data servis dari controller, $value = satu baris data di setiap putaran --}}
                        @foreach ($rows as $value)
                            <tr>
                                <td class="text-center">{{ $no }}</td>
                                <?php $no++; ?>
                                <td>{{ $value->nama_pelanggan }}</td>
                                <td>{{ $value->no_polisi }}</td>
                                <td>{{ $value->daftar_jasa }}</td>
                                <td class="text-end">Rp{{ number_format($value->total_jasa, 0, ',', '.') }}</td>
                                <?php if ($value->nama_mekanik == '') { ?>
                                    <td>-</td>
                                <?php } else { ?>
                                    <td>{{ $value->nama_mekanik }}</td>
                                <?php } ?>
                                <?php if ($value->metode_pembayaran == '') { ?>
                                    <td>-</td>
                                <?php } else { ?>
                                    <td>{{ $value->metode_pembayaran }}</td>
                                <?php } ?>
                                <td>{{ $value->tanggal_masuk }}</td>
                                <td class="text-center"><span class="badge rounded-pill px-3 py-2 {{ $statusBadge[$value->status] ?? 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle' }}">{{ ucfirst($value->status) }}</span></td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1 align-items-center">
                                        <?php if ($canWrite) { ?>
                                        <a href="/edit_servis/{{ $value->id }}" class="btn btn-warning text-dark fw-semibold btn-sm rounded-3" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                        <form action="/hapus_servis/{{ $value->id }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm rounded-3" title="Hapus"><i class="bi bi-trash"></i></button>
                                        </form>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
