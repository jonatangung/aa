@extends('template')

@section('content')

    <h1 class="mb-3">Manajemen Pengguna</h1>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2 no-print">
        <a href="/tambah_users" class="btn btn-secondary">Tambah Pengguna</a>
      
    </div>

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
                    @foreach ($rows as $value)
                        <tr>
                            <td><?= $no++ ?></td>
                            <td class="text-center">
                                @if ($value->foto)
                                    <img src="{{ asset('uploads/' . $value->foto) }}" width="50" height="50" class="rounded">
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td><?= $value->nama ?></td>
                            <td><?= $value->email ?></td>
                            <td><?= $value->role ?></td>
                            <td><?= $value->no_hp ?></td>
                            <td><?= $value->is_active ? 'Aktif' : 'Tidak Aktif' ?></td>
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
