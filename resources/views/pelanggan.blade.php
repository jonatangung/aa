@extends('template')

@section('content')
{{-- Halaman daftar pelanggan. Data $pelanggan dikirim dari PelangganController method pelanggan() --}}

    <?php $canWrite = \App\Support\RolePermission::boleh('write:pelanggan'); ?>
    <?php $canExport = \App\Support\RolePermission::boleh('export:pelanggan'); ?>

    <div class="card shadow-sm border-0 border-top border-warning border-3">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="card-title mb-0">Data Pelanggan</h5>
            <div class="d-flex gap-2 flex-wrap no-print">
                <?php if ($canWrite) { ?>
                <a href="/tplg" class="btn btn-warning text-dark fw-semibold btn-sm rounded-pill"><i class="bi bi-plus-lg-circle"></i> Tambah Pelanggan</a>
                <?php } ?>
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
                <?php if ($canExport) { ?>
                <a href="/pelanggan/pdf" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                <a href="/pelanggan/excel" class="btn btn-outline-success btn-sm rounded-pill"><i class="bi bi-file-earmark-excel"></i> Excel</a>
                <?php } ?>
            </div>
        </div>

        <div class="card-body">
            {{-- Tabel daftar pelanggan --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th>Nama Pelanggan</th>
                            <th>Alamat</th>
                            <th>Email</th>
                            <th>No HP</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        {{-- $pelanggan = kumpulan data pelanggan dari controller, $value = satu baris data di setiap putaran --}}
                        @foreach ($pelanggan as $value)
                            <tr>
                                <td class="text-center">{{ $no }}</td>
                                <?php $no++; ?>
                                <td>{{ $value->nama }}</td>
                                <td>{{ $value->alamat }}</td>
                                <td>{{ $value->email }}</td>
                                <td>{{ $value->no_hp }}</td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1 align-items-center">
                                        <?php if ($canWrite) { ?>
                                        <a href="/edit_pelanggan/{{ $value->id }}" class="btn btn-warning text-dark fw-semibold btn-sm rounded-3" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                        <form action="/hapus_pelanggan/{{ $value->id }}" method="POST" class="d-inline">
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
