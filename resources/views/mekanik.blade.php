@extends('template')

@section('content')
{{-- Halaman daftar mekanik. Data $karyawan dikirim dari MekanikController method karyawan() --}}

    <?php $canWrite = \App\Support\RolePermission::boleh('write:mekanik'); ?>

    <div class="card shadow-sm border-0 border-top border-warning border-3">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="card-title mb-0">Data Mekanik</h5>
            <div class="d-flex gap-2 flex-wrap no-print">
                <?php if ($canWrite) { ?>
                <a href="/tkry" class="btn btn-warning text-dark fw-semibold btn-sm rounded-pill"><i class="bi bi-plus-lg-circle"></i> Tambah Mekanik</a>
                <?php } ?>
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
                <a href="/mekanik/pdf" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                <a href="/mekanik/excel" class="btn btn-outline-success btn-sm rounded-pill"><i class="bi bi-file-earmark-excel"></i> Excel</a>
            </div>
        </div>

        <div class="card-body">
            {{-- Tabel daftar mekanik --}}
            <table class="table table-hover table-striped align-middle datatable">
                <thead class="table-warning">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>No HP</th>
                        <th>Spesialisasi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- $no = variabel nomor urut, dimulai dari 1 --}}
                    <?php $no = 1; ?>
                    {{-- $karyawan = kumpulan data mekanik dari controller, $value = satu baris data di setiap putaran --}}
                    @foreach ($karyawan as $value)
                        <tr>
                            {{-- Tampilkan nomor urut --}}
                            <td>{{ $no }}</td>
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
                            <td>
                                <?php if ($value->status == 'aktif') { ?>
                                    <span class="badge rounded-pill bg-success">Aktif</span>
                                <?php } else { ?>
                                    <span class="badge rounded-pill bg-secondary">Nonaktif</span>
                                <?php } ?>
                            </td>
                            {{-- Tombol edit dan hapus --}}
                            <td>
                                <div class="d-flex gap-1">
                                    <?php if ($canWrite) { ?>
                                    <a href="/edit_mekanik/{{ $value->id }}" class="btn btn-warning text-dark fw-semibold btn-sm rounded-pill" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                    <form action="/hapus_mekanik/{{ $value->id }}" method="POST" class="d-inline">
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
