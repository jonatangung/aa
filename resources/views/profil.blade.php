@extends('template')

@section('content')
{{-- Halaman pengaturan profil. Data $user dikirim dari ProfilController method profil(), form dikirim ke method updateProfil() --}}

    <h1 class="mb-4">Pengaturan Profil</h1>

    <div class="card">
        <div class="card-body">
            <?php if ($errors->any()) { ?>
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            <?php } ?>

            <?php if ($user != null) { ?>
                <form method="POST" action="/profil" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Pengguna</label>
                        <input type="text" name="nama" class="form-control" value="{{ old('nama', $user->nama) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">No. HP</label>
                        <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $user->no_hp) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password Baru</label>
                        <input type="password" name="password" class="form-control" minlength="6" placeholder="Kosongkan jika tidak ingin mengganti password">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Foto Profil</label>
                        <input type="file" name="foto" class="form-control" accept="image/*">
                        <?php if ($user->foto != null && $user->foto != '') { ?>
                            <img src="{{ asset('uploads/' . $user->foto) }}" width="100" class="mt-2 rounded">
                        <?php } ?>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Profil</button>
                </form>
            <?php } else { ?>
                <p class="text-muted mb-0">Data profil tidak ditemukan.</p>
            <?php } ?>
        </div>
    </div>

@endsection
