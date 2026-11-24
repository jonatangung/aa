@extends('template')

@section('content')
{{-- Halaman pengaturan profil. Data $user dikirim dari ProfilController method profil(), form dikirim ke method updateProfil() --}}

    <div class="card shadow-sm border-0 border-top border-warning border-3">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">Pengaturan Profil</h5>
        </div>

        <div class="card-body">
            <?php if ($errors->any()) { ?>
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            <?php } ?>

            <?php if ($user != null) { ?>
                <form method="POST" action="/profil" enctype="multipart/form-data" class="row g-3">
                    @csrf

                    <div class="col-md-6">
                        <label class="form-label">Nama Pengguna</label>
                        <input type="text" name="nama" class="form-control" value="{{ old('nama', $user->nama) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">No. HP</label>
                        <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $user->no_hp) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password" class="form-control" minlength="6" placeholder="Kosongkan jika tidak ingin mengganti password">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Foto Profil</label>
                        <input type="file" name="foto" class="form-control" accept="image/*">
                        <?php if ($user->foto != null && $user->foto != '' && file_exists(public_path('uploads/' . $user->foto))) { ?>
                            <img src="{{ asset('uploads/' . $user->foto) }}" width="90" height="90" class="mt-3 rounded-circle" style="object-fit: cover;">
                        <?php } else { ?>
                            <img src="{{ asset('assets/img/profile-img.jpg') }}" width="90" height="90" class="mt-3 rounded-circle" style="object-fit: cover;">
                        <?php } ?>
                    </div>

                    <div class="col-12 d-flex gap-2 mt-2">
                        <button type="submit" class="btn btn-warning text-white fw-semibold rounded-pill"><i class="bi bi-check-lg-circle"></i> Simpan Profil</button>
                        <a href="/home" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-arrow-left"></i> Kembali</a>
                    </div>
                </form>
            <?php } else { ?>
                <p class="text-white-50 mb-0">Data profil tidak ditemukan.</p>
            <?php } ?>
        </div>
    </div>

@endsection
