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
                <button type="button" class="btn btn-warning text-white fw-semibold btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalTambahPelanggan"><i class="bi bi-plus-lg-circle"></i> Tambah Pelanggan</button>
                <?php } ?>
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
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
                                        <a href="/edit_pelanggan/{{ $value->id }}" class="btn btn-warning text-white fw-semibold btn-sm rounded-3" title="Edit"><i class="bi bi-pencil-square"></i></a>
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

    {{-- Modal Tambah Pelanggan --}}
    <div class="modal fade modal-gold no-print" id="modalTambahPelanggan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus"></i> Tambah Pelanggan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/aksi_tplg" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Nama Pelanggan</label>
                                <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">No HP</label>
                                <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Alamat</label>
                                <input type="text" name="alamat" class="form-control" value="{{ old('alamat') }}" required>
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
