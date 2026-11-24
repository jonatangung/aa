@extends('template')

@section('content')
{{-- Halaman daftar kendaraan. Data $rows dikirim dari KendaraanController method kendaraan() --}}

    <?php $canWrite = \App\Support\RolePermission::boleh('write:kendaraan'); ?>
    <?php $canExport = \App\Support\RolePermission::boleh('export:kendaraan'); ?>

    <div class="card shadow-sm border-0 border-top border-warning border-3">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="card-title mb-0">Data Kendaraan</h5>
            <div class="d-flex gap-2 flex-wrap no-print">
                <?php if ($canWrite) { ?>
                <button type="button" class="btn btn-warning text-white fw-semibold btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalTambahKendaraan"><i class="bi bi-plus-lg-circle"></i> Tambah Kendaraan</button>
                <?php } ?>
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
                <?php if ($canExport) { ?>
                <a href="/kendaraan/pdf" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                <a href="/kendaraan/excel" class="btn btn-outline-success btn-sm rounded-pill"><i class="bi bi-file-earmark-excel"></i> Excel</a>
                <?php } ?>
            </div>
        </div>

        <div class="card-body">
            {{-- Tabel daftar kendaraan --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th>No Polisi</th>
                            <th>Pelanggan</th>
                            <th>Merk</th>
                            <th>Model</th>
                            <th>Tahun</th>
                            <th>Warna</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        {{-- $rows = kumpulan data kendaraan dari controller, $value = satu baris data di setiap putaran --}}
                        @foreach ($rows as $value)
                            <tr>
                                <td class="text-center">{{ $no }}</td>
                                <?php $no++; ?>
                                <td>{{ $value->no_polisi }}</td>
                                <td>{{ $value->nama_pelanggan }}</td>
                                <td>{{ $value->merk }}</td>
                                <td>{{ $value->model }}</td>
                                <td>{{ $value->tahun }}</td>
                                <td>{{ $value->warna }}</td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1 align-items-center">
                                        <?php if ($canWrite) { ?>
                                        <a href="/edit_kendaraan/{{ $value->id }}" class="btn btn-warning text-white fw-semibold btn-sm rounded-3" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                        <form action="/hapus_kendaraan/{{ $value->id }}" method="POST" class="d-inline">
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

    {{-- Modal Tambah Kendaraan --}}
    <div class="modal fade modal-gold no-print" id="modalTambahKendaraan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-car-front"></i> Tambah Kendaraan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/tambah_kendaraan_aksi" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Pelanggan</label>
                                <select name="pelanggan_id" class="form-select" required>
                                    <option value="">-- Pilih Pelanggan --</option>
                                    @foreach ($pelanggan as $item)
                                        <option value="{{ $item->id }}" @selected(old('pelanggan_id') == $item->id)>
                                            {{ $item->kode_pelanggan }} - {{ $item->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No Polisi</label>
                                <input type="text" name="no_polisi" class="form-control" value="{{ old('no_polisi') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Merk</label>
                                <input type="text" name="merk" class="form-control" value="{{ old('merk') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Model</label>
                                <input type="text" name="model" class="form-control" value="{{ old('model') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tahun</label>
                                <input type="number" name="tahun" class="form-control" value="{{ old('tahun') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Warna</label>
                                <input type="text" name="warna" class="form-control" value="{{ old('warna') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">No Rangka</label>
                                <input type="text" name="no_rangka" class="form-control" value="{{ old('no_rangka') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No Mesin</label>
                                <input type="text" name="no_mesin" class="form-control" value="{{ old('no_mesin') }}">
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
