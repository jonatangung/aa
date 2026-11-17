@extends('template')

@section('content')
{{-- Halaman daftar pengguna. Data $rows dikirim dari PenggunaController method pengguna() --}}

    <?php $canWrite = \App\Support\RolePermission::boleh('write:pengguna'); ?>

    <div class="card shadow-sm border-0 border-top border-warning border-3">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="card-title mb-0">Manajemen Pengguna</h5>
            <div class="d-flex gap-2 flex-wrap no-print">
                <?php if ($canWrite) { ?>
                <a href="/tambah_users" class="btn btn-warning text-dark fw-semibold btn-sm rounded-pill"><i class="bi bi-person-plus"></i> Tambah Pengguna</a>
                <?php } ?>
            </div>
        </div>

        <div class="card-body">
            {{-- Tabel daftar pengguna --}}
            <table class="table table-hover table-striped align-middle datatable">
                <thead class="table-warning">
                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>No HP</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    {{-- $rows = kumpulan data pengguna dari controller, $value = satu baris data di setiap putaran --}}
                    @foreach ($rows as $value)
                        <tr>
                            <td>{{ $no }}</td>
                            <?php $no++; ?>
                            <td>
                                <?php if ($value->foto != null && $value->foto != '') { ?>
                                    <img src="{{ asset('uploads/' . $value->foto) }}" width="40" height="40" class="rounded-circle">
                                <?php } else { ?>
                                    <span class="badge bg-secondary rounded-circle p-2">-</span>
                                <?php } ?>
                            </td>
                            <td>{{ $value->nama }}</td>
                            <td>{{ $value->email }}</td>
                            <td><span class="badge rounded-pill bg-primary">{{ ucfirst($value->role) }}</span></td>
                            <td>{{ $value->no_hp }}</td>
                            <?php if ($value->is_active == 1) { ?>
                                <td><span class="badge rounded-pill bg-success">Aktif</span></td>
                            <?php } else { ?>
                                <td><span class="badge rounded-pill bg-secondary">Tidak Aktif</span></td>
                            <?php } ?>
                            <td>
                                <?php if ($canWrite) { ?>
                                <a href="/edit_users/{{ $value->id }}" class="btn btn-warning text-dark fw-semibold btn-sm rounded-pill" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                <?php } ?>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
