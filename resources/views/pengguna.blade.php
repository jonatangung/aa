@extends('template')

@section('content')
{{-- Halaman daftar pengguna. Data $rows dikirim dari PenggunaController method pengguna() --}}

    <h1 class="mb-3">Manajemen Pengguna</h1>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2 no-print">
        <a href="/tambah_users" class="btn btn-secondary">Tambah Pengguna</a>
    </div>

    {{-- Tabel daftar pengguna --}}
    <div class="card mt-3">
        <div class="card-body table-responsive">
            <h5 class="card-title">Manajemen Pengguna</h5>

            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark text-center">
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
                            <td class="text-center">
                                <?php if ($value->foto != null && $value->foto != '') { ?>
                                    <img src="{{ asset('uploads/' . $value->foto) }}" width="50" height="50" class="rounded">
                                <?php } else { ?>
                                    <span class="text-muted">-</span>
                                <?php } ?>
                            </td>
                            <td>{{ $value->nama }}</td>
                            <td>{{ $value->email }}</td>
                            <td>{{ $value->role }}</td>
                            <td>{{ $value->no_hp }}</td>
                            <?php if ($value->is_active == 1) { ?>
                                <td>Aktif</td>
                            <?php } else { ?>
                                <td>Tidak Aktif</td>
                            <?php } ?>
                            <td>
                                <a href="/edit_users/{{ $value->id }}" class="btn btn-sm btn-warning">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
