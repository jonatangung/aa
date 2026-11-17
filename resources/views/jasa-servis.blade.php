@extends('template')

@section('content')
{{-- Halaman daftar jasa servis. Data $rows dikirim dari JasaServisController method jasaServis() --}}

    <?php $canWrite = \App\Support\RolePermission::boleh('write:jasa_servis'); ?>
    <?php $canExport = \App\Support\RolePermission::boleh('export:jasa_servis'); ?>

    <div class="card shadow-sm border-0 border-top border-warning border-3">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="card-title mb-0">Data Jasa Servis</h5>
            <div class="d-flex gap-2 flex-wrap no-print">
                <?php if ($canWrite) { ?>
                <a href="/tambah_jasa_servis" class="btn btn-warning text-dark fw-semibold btn-sm rounded-pill"><i class="bi bi-plus-lg-circle"></i> Tambah Jasa Servis</a>
                <?php } ?>
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
                <?php if ($canExport) { ?>
                <a href="/jasa-servis/pdf" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                <a href="/jasa-servis/excel" class="btn btn-outline-success btn-sm rounded-pill"><i class="bi bi-file-earmark-excel"></i> Excel</a>
                <?php } ?>
            </div>
        </div>

        <div class="card-body">
            {{-- Tabel daftar jasa servis --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th>Kode Jasa</th>
                            <th>Nama Jasa</th>
                            <th class="text-end">Harga</th>
                            <th>Estimasi Menit</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        {{-- $rows = kumpulan data jasa servis dari controller, $value = satu baris data di setiap putaran --}}
                        @foreach ($rows as $value)
                            <tr>
                                <td class="text-center">{{ $no }}</td>
                                <?php $no++; ?>
                                <td>{{ $value->kode_jasa }}</td>
                                <td>{{ $value->nama_jasa }}</td>
                                <td class="text-end">Rp{{ number_format($value->harga, 0, ',', '.') }}</td>
                                <td>{{ $value->estimasi_menit }}</td>
                                <?php if ($value->is_active == 1) { ?>
                                    <td class="text-center"><span class="badge rounded-pill bg-success-subtle text-success-emphasis border border-success-subtle px-3 py-2">Aktif</span></td>
                                <?php } else { ?>
                                    <td class="text-center"><span class="badge rounded-pill bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle px-3 py-2">Tidak Aktif</span></td>
                                <?php } ?>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1 align-items-center">
                                        <?php if ($canWrite) { ?>
                                        <a href="/edit_jasa_servis/{{ $value->id }}" class="btn btn-warning text-dark fw-semibold btn-sm rounded-3" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                        <form action="/hapus_jasa_servis/{{ $value->id }}" method="POST" class="d-inline">
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
