@extends('template')

@section('content')
{{-- Halaman daftar supplier. Data $rows dikirim dari SupplierController method supplier() --}}

    <?php $canWrite = \App\Support\RolePermission::boleh('write:supplier'); ?>
    <?php $canExport = \App\Support\RolePermission::boleh('export:supplier'); ?>

    <div class="card shadow-sm border-0 border-top border-warning border-3">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="card-title mb-0">Data Supplier</h5>
            <div class="d-flex gap-2 flex-wrap no-print">
                <?php if ($canWrite) { ?>
                <button type="button" class="btn btn-warning text-white fw-semibold btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalTambahSupplier"><i class="bi bi-plus-lg-circle"></i> Tambah Supplier</button>
                <?php } ?>
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
                <?php if ($canExport) { ?>
                <a href="/supplier/pdf" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                <a href="/supplier/excel" class="btn btn-outline-success btn-sm rounded-pill"><i class="bi bi-file-earmark-excel"></i> Excel</a>
                <?php } ?>
            </div>
        </div>

        <div class="card-body">
            {{-- Tabel daftar supplier --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th>Nama Supplier / PT</th>
                            <th>Nama Barang</th>
                            <th>Stok</th>
                            <th class="text-end">Harga</th>
                            <th>Tanggal Masuk</th>
                            <th>No HP</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        {{-- $rows = kumpulan data supplier dari controller, $value = satu baris data di setiap putaran --}}
                        @foreach ($rows as $value)
                            <tr>
                                <td class="text-center">{{ $no }}</td>
                                <?php $no++; ?>
                                <td>{{ $value->nama_supplier }}</td>
                                <?php if ($value->nama_barang == '') { ?>
                                    <td>-</td>
                                <?php } else { ?>
                                    <td>{{ $value->nama_barang }}</td>
                                <?php } ?>
                                <td>{{ $value->stok }}</td>
                                <td class="text-end">Rp{{ number_format($value->harga, 0, ',', '.') }}</td>
                                <?php if ($value->tanggal_masuk == '') { ?>
                                    <td>-</td>
                                <?php } else { ?>
                                    <td>{{ $value->tanggal_masuk }}</td>
                                <?php } ?>
                                <td>{{ $value->no_hp }}</td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1 align-items-center">
                                        <?php if ($canWrite) { ?>
                                        <a href="/edit_supplier/{{ $value->id }}" class="btn btn-warning text-white fw-semibold btn-sm rounded-3" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                        <form action="/hapus_supplier/{{ $value->id }}" method="POST" class="d-inline">
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

    {{-- Modal Tambah Supplier --}}
    <div class="modal fade modal-gold no-print" id="modalTambahSupplier" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-truck"></i> Tambah Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/tambah_supplier_aksi" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Supplier</label>
                                <input type="text" name="nama_supplier" class="form-control" value="{{ old('nama_supplier') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kontak Person</label>
                                <input type="text" name="kontak_person" class="form-control" value="{{ old('kontak_person') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No HP</label>
                                <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Alamat</label>
                                <textarea name="alamat" class="form-control" rows="2">{{ old('alamat') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Barang</label>
                                <input type="text" name="nama_barang" class="form-control" value="{{ old('nama_barang') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Stok</label>
                                <input type="number" name="stok" class="form-control" value="{{ old('stok', 0) }}" min="0" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Harga</label>
                                <input type="number" name="harga" class="form-control" value="{{ old('harga', 0) }}" min="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Masuk</label>
                                <input type="date" name="tanggal_masuk" class="form-control" value="{{ old('tanggal_masuk', date('Y-m-d')) }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i> Batal</button>
                        <button type="submit" class="btn btn-warning text-white fw-semibold rounded-pill"><i class="bi bi-check-lg-circle"></i> Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
