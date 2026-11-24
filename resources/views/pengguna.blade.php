@extends('template')

@section('content')
{{-- Halaman daftar pengguna. Data $rows dikirim dari PenggunaController method pengguna() --}}

    <?php $canWrite = \App\Support\RolePermission::boleh('write:pengguna'); ?>

    <div class="card shadow-sm border-0 border-top border-warning border-3">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="card-title mb-0">Manajemen Pengguna</h5>
            <div class="d-flex gap-2 flex-wrap no-print">
                <?php if ($canWrite) { ?>
                <button type="button" class="btn btn-warning text-white fw-semibold btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalTambahPengguna"><i class="bi bi-person-plus"></i> Tambah Pengguna</button>
                <?php } ?>
            </div>
        </div>

        <div class="card-body">
            {{-- Tabel daftar pengguna --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Foto</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>No HP</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php $roleBadges = ['owner' => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle', 'admin' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle', 'mekanik' => 'bg-dark-subtle text-dark-emphasis border border-dark-subtle', 'kasir' => 'bg-success-subtle text-success-emphasis border border-success-subtle']; ?>
                        {{-- $rows = kumpulan data pengguna dari controller, $value = satu baris data di setiap putaran --}}
                        @foreach ($rows as $value)
                            <tr>
                                <td class="text-center">{{ $no }}</td>
                                <?php $no++; ?>
                                <td class="text-center">
                                    <?php if ($value->foto != null && $value->foto != '' && file_exists(public_path('uploads/' . $value->foto))) { ?>
                                        <img src="{{ asset('uploads/' . $value->foto) }}" width="42" height="42" class="rounded-circle object-fit-cover shadow-sm">
                                    <?php } else { ?>
                                        <img src="{{ asset('assets/img/profile-img.jpg') }}" width="42" height="42" class="rounded-circle object-fit-cover shadow-sm">
                                    <?php } ?>
                                </td>
                                <td>{{ $value->nama }}</td>
                                <td>{{ $value->email }}</td>
                                <td><span class="badge rounded-pill px-3 py-2 {{ $roleBadges[strtolower($value->role)] ?? 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle' }}">{{ ucfirst($value->role) }}</span></td>
                                <td>{{ $value->no_hp }}</td>
                                <?php if ($value->is_active == 1) { ?>
                                    <td class="text-center"><span class="badge rounded-pill bg-success-subtle text-success-emphasis border border-success-subtle px-3 py-2">Aktif</span></td>
                                <?php } else { ?>
                                    <td class="text-center"><span class="badge rounded-pill bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle px-3 py-2">Tidak Aktif</span></td>
                                <?php } ?>
                                <td class="text-center">
                                    <?php if ($canWrite) { ?>
                                    <div class="d-inline-flex gap-1 align-items-center">
                                        <a href="/edit_users/{{ $value->id }}" class="btn btn-warning text-white fw-semibold btn-sm rounded-3" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                    </div>
                                    <?php } ?>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Tambah Pengguna --}}
    <div class="modal fade modal-gold no-print" id="modalTambahPengguna" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus"></i> Tambah Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/tambah_users_aksi" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Nama</label>
                                <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">No HP</label>
                                <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Role</label>
                                <select name="role" class="form-select" required>
                                    <option value="">-- Pilih Role --</option>
                                    @foreach (['owner' => 'Owner', 'admin' => 'Admin', 'kasir' => 'Kasir', 'karyawan' => 'Karyawan', 'mekanik' => 'Mekanik'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('role') == $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Foto</label>
                                <input type="file" name="foto" class="form-control" accept="image/*">
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
