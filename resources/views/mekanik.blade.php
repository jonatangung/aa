@extends('template')

@section('content')
{{-- Halaman daftar mekanik. Data $karyawan dikirim dari MekanikController method karyawan() --}}

    <?php $canWrite = \App\Support\RolePermission::boleh('write:mekanik'); ?>
    <?php $canExport = \App\Support\RolePermission::boleh('export:mekanik'); ?>

    <div class="card shadow-sm border-0 border-top border-warning border-3">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="card-title mb-0">Data Mekanik</h5>
            <div class="d-flex gap-2 flex-wrap no-print">
                <?php if ($canWrite) { ?>
                <button type="button" class="btn btn-warning text-white fw-semibold btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalTambahMekanik"><i class="bi bi-plus-lg-circle"></i> Tambah Mekanik</button>
                <?php } ?>
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
                <?php if ($canExport) { ?>
                <a href="/mekanik/pdf" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                <a href="/mekanik/excel" class="btn btn-outline-success btn-sm rounded-pill"><i class="bi bi-file-earmark-excel"></i> Excel</a>
                <?php } ?>
            </div>
        </div>

        <div class="card-body">
            {{-- Tabel daftar mekanik --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th>Nama</th>
                            <th>No HP</th>
                            <th>Spesialisasi</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- $no = variabel nomor urut, dimulai dari 1 --}}
                        <?php $no = 1; ?>
                        {{-- $karyawan = kumpulan data mekanik dari controller, $value = satu baris data di setiap putaran --}}
                        @foreach ($karyawan as $value)
                            <tr>
                                {{-- Tampilkan nomor urut --}}
                                <td class="text-center">{{ $no }}</td>
                                <?php $no++; ?>
                                <td>{{ $value->nama }}</td>
                                {{-- Tampilkan no HP, jika kosong tampilkan tanda - --}}
                                <td>
                                    <?php if ($value->no_hp == '') { ?>
                                        -
                                    <?php } else { ?>
                                        {{ $value->no_hp }}
                                    <?php } ?>
                                </td>
                                {{-- Tampilkan spesialisasi, jika kosong tampilkan tanda - --}}
                                <td>
                                    <?php if ($value->spesialisasi == '') { ?>
                                        -
                                    <?php } else { ?>
                                        {{ $value->spesialisasi }}
                                    <?php } ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($value->status == 'aktif') { ?>
                                        <span class="badge rounded-pill bg-success-subtle text-success-emphasis border border-success-subtle px-3 py-2">Aktif</span>
                                    <?php } else { ?>
                                        <span class="badge rounded-pill bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle px-3 py-2">Nonaktif</span>
                                    <?php } ?>
                                </td>
                                {{-- Tombol edit dan hapus --}}
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1 align-items-center">
                                        <?php if ($canWrite) { ?>
                                        <a href="/edit_mekanik/{{ $value->id }}" class="btn btn-warning text-white fw-semibold btn-sm rounded-3" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                        <form action="/hapus_mekanik/{{ $value->id }}" method="POST" class="d-inline">
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

    {{-- Modal Tambah Mekanik --}}
    <div class="modal fade modal-gold no-print" id="modalTambahMekanik" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-tools"></i> Tambah Mekanik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/aksi_tkry" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Nama Mekanik</label>
                                <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">No HP</label>
                                <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Spesialisasi</label>
                                <input type="text" name="spesialisasi" class="form-control" value="{{ old('spesialisasi') }}">
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
