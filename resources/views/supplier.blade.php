@extends('template')

@section('content')
{{-- Halaman daftar supplier. Data $rows dikirim dari SupplierController method supplier() --}}

    <?php $canWrite = \App\Support\RolePermission::boleh('write:supplier'); ?>

    <div class="card shadow-sm border-0 border-top border-warning border-3">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="card-title mb-0">Data Supplier</h5>
            <div class="d-flex gap-2 flex-wrap no-print">
                <?php if ($canWrite) { ?>
                <a href="/tambah_supplier" class="btn btn-warning text-dark fw-semibold btn-sm rounded-pill"><i class="bi bi-plus-lg-circle"></i> Tambah Supplier</a>
                <?php } ?>
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
                <a href="/supplier/pdf" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                <a href="/supplier/excel" class="btn btn-outline-success btn-sm rounded-pill"><i class="bi bi-file-earmark-excel"></i> Excel</a>
            </div>
        </div>

        <div class="card-body">
            {{-- Tabel daftar supplier --}}
            <table class="table table-hover table-striped align-middle datatable">
                <thead class="table-warning">
                    <tr>
                        <th>No</th>
                        <th>Nama Supplier / PT</th>
                        <th>Nama Barang</th>
                        <th>Stok</th>
                        <th>Harga</th>
                        <th>Tanggal Masuk</th>
                        <th>No HP</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    {{-- $rows = kumpulan data supplier dari controller, $value = satu baris data di setiap putaran --}}
                    @foreach ($rows as $value)
                        <tr>
                            <td>{{ $no }}</td>
                            <?php $no++; ?>
                            <td>{{ $value->nama_supplier }}</td>
                            <?php if ($value->nama_barang == '') { ?>
                                <td>-</td>
                            <?php } else { ?>
                                <td>{{ $value->nama_barang }}</td>
                            <?php } ?>
                            <td>{{ $value->stok }}</td>
                            <td>Rp{{ number_format($value->harga, 0, ',', '.') }}</td>
                            <?php if ($value->tanggal_masuk == '') { ?>
                                <td>-</td>
                            <?php } else { ?>
                                <td>{{ $value->tanggal_masuk }}</td>
                            <?php } ?>
                            <td>{{ $value->no_hp }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <?php if ($canWrite) { ?>
                                    <a href="/edit_supplier/{{ $value->id }}" class="btn btn-warning text-dark fw-semibold btn-sm rounded-pill" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                    <form action="/hapus_supplier/{{ $value->id }}" method="POST" class="d-inline">
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
