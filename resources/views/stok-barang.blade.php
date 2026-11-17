@extends('template')

@section('content')
{{-- Halaman rekap stok barang. Data $rows dikirim dari StokBarangController method stokBarang() --}}

    <?php $canWrite = \App\Support\RolePermission::boleh('write:stok_barang'); ?>
    <?php $canExport = \App\Support\RolePermission::boleh('export:stok_barang'); ?>

    <div class="card shadow-sm border-0 border-top border-warning border-3">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="card-title mb-0">Stok Barang</h5>
            <div class="d-flex gap-2 flex-wrap no-print">
                <?php if ($canWrite) { ?>
                <a href="/input_stok_barang" class="btn btn-warning text-dark fw-semibold btn-sm rounded-pill"><i class="bi bi-boxes"></i> Kelola Stok</a>
                <?php } ?>
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
                <?php if ($canExport) { ?>
                <a href="/stok-barang/pdf" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                <a href="/stok-barang/excel" class="btn btn-outline-success btn-sm rounded-pill"><i class="bi bi-file-earmark-excel"></i> Excel</a>
                <?php } ?>
            </div>
        </div>

        <div class="card-body">
            {{-- Tabel rekap stok per barang --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th>Supplier</th>
                            <th>Nama Barang</th>
                            <th class="text-end">Harga</th>
                            <th>Stok Sekarang</th>
                            <th>Stok Masuk</th>
                            <th>Tanggal Masuk</th>
                            <th>Stok Keluar</th>
                            <th>Tanggal Keluar</th>
                            <th class="text-end">Harga Jual</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        {{-- $rows = rekap stok dari controller, $row = satu baris data di setiap putaran --}}
                        @foreach ($rows as $row)
                            <tr>
                                <td class="text-center">{{ $no }}</td>
                                <?php $no++; ?>
                                <td>{{ $row->nama_supplier }}</td>
                                <?php if ($row->nama_barang == null || $row->nama_barang == '') { ?>
                                    <td>-</td>
                                <?php } else { ?>
                                    <td>{{ $row->nama_barang }}</td>
                                <?php } ?>
                                <td class="text-end">Rp{{ number_format($row->harga, 0, ',', '.') }}</td>
                                <td>{{ $row->stok }}</td>
                                <td>{{ $row->stok_masuk }}</td>
                                <td>{{ $row->tanggal_masuk }}</td>
                                <td>{{ $row->stok_keluar }}</td>
                                <td>{{ $row->tanggal_keluar }}</td>
                                <td class="text-end">Rp{{ number_format($row->harga_jual, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
