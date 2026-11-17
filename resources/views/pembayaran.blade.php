@extends('template')

@section('content')
{{-- Halaman daftar pembayaran. Data $rows dikirim dari PembayaranController method pembayaran() --}}

    <?php $canWrite = \App\Support\RolePermission::boleh('write:pembayaran'); ?>

    <div class="card shadow-sm border-0 border-top border-warning border-3">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="card-title mb-0">Pembayaran</h5>
            <div class="d-flex gap-2 flex-wrap no-print">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
                <a href="/pembayaran/pdf" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                <a href="/pembayaran/excel" class="btn btn-outline-success btn-sm rounded-pill"><i class="bi bi-file-earmark-excel"></i> Excel</a>
            </div>
        </div>

        <div class="card-body">
            {{-- Tabel daftar pembayaran --}}
            <table class="table table-hover table-striped align-middle datatable">
                <thead class="table-warning">
                    <tr>
                        <th>No</th>
                        <th>No Pembayaran</th>
                        <th>No Servis</th>
                        <th>Keluhan</th>
                        <th>Jasa Dipilih</th>
                        <th>Tanggal</th>
                        <th>Jumlah Bayar</th>
                        <th>Metode</th>
                        <th>Aksi</th>
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
                            <?php if ($value->no_servis == '') { ?>
                                <td>-</td>
                            <?php } else { ?>
                                <td>{{ $value->no_servis }}</td>
                            <?php } ?>
                            <?php if ($value->keluhan == '') { ?>
                                <td>-</td>
                            <?php } else { ?>
                                <td>{{ $value->keluhan }}</td>
                            <?php } ?>
                            {{-- $value->daftar_jasa = rincian jasa yang sudah dirangkai di controller --}}
                            <td>{{ $value->daftar_jasa }}</td>
                            <td>{{ $value->tanggal }}</td>
                            <td class="fw-bold">Rp{{ number_format($value->jumlah_bayar, 0, ',', '.') }}</td>
                            <td><span class="badge rounded-pill bg-info text-dark">{{ ucfirst($value->metode) }}</span></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <?php if ($canWrite) { ?>
                                    <a href="/edit_pembayaran/{{ $value->id }}" class="btn btn-warning text-dark fw-semibold btn-sm rounded-pill" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                    <form action="/hapus_pembayaran/{{ $value->id }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm rounded-pill" title="Hapus"><i class="bi bi-trash"></i></button>
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

@endsection
