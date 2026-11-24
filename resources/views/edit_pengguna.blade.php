@extends('template')

@section('content')
{{-- Halaman form edit pengguna. Data $row dikirim dari PenggunaController method editPengguna(). Data dikirim ke method updatePengguna() --}}

    <div class="card shadow-sm border-0 border-top border-warning border-3">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">Edit Pengguna</h5>
        </div>

        <div class="card-body">
            <?php if ($errors->any()) { ?>
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            <?php } ?>

            <form action="/edit_users_aksi/{{ $row->id }}" method="POST" enctype="multipart/form-data" class="row g-3">
                @csrf

                <div class="col-md-6">
                    <label class="form-label">Nama</label>
                    <input type="text" name="nama" value="{{ $row->nama }}" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">No HP</label>
                    <input type="text" name="no_hp" value="{{ $row->no_hp }}" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ $row->email }}" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select" required>
                        @foreach (['owner' => 'Owner', 'admin' => 'Admin', 'kasir' => 'Kasir', 'karyawan' => 'Karyawan', 'mekanik' => 'Mekanik'] as $value => $label)
                            <option value="{{ $value }}" @selected($row->role == $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Foto Profil</label>
                    <input type="file" name="foto" class="form-control" accept="image/*">
                    <?php if ($row->foto != null && $row->foto != '' && file_exists(public_path('uploads/' . $row->foto))) { ?>
                        <img src="{{ asset('uploads/' . $row->foto) }}" width="80" height="80" class="mt-2 rounded-circle" style="object-fit: cover;">
                    <?php } else { ?>
                        <img src="{{ asset('assets/img/profile-img.jpg') }}" width="80" height="80" class="mt-2 rounded-circle" style="object-fit: cover;">
                    <?php } ?>
                </div>

                <div class="col-12 d-flex gap-2 mt-2">
                    <button class="btn btn-warning rounded-pill" type="submit"><i class="bi bi-check-lg-circle"></i> Perbarui</button>
                    <a href="/pengguna" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div>
    </div>

@endsection
