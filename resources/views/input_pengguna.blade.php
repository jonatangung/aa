@extends('template')

@section('content')
{{-- Halaman form tambah pengguna baru. Tidak ada data dari controller, form dikirim ke PenggunaController method simpanPengguna() --}}

    <div class="card shadow-sm border-0 border-top border-warning border-3">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">Tambah Pengguna</h5>
        </div>

        <div class="card-body">
            <?php if ($errors->any()) { ?>
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            <?php } ?>

            <form action="/tambah_users_aksi" method="POST" enctype="multipart/form-data" class="row g-3">
                @csrf

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
                        {{-- Daftar pilihan role, $value = nilai yang disimpan, $label = teks yang tampil --}}
                        @foreach (['owner' => 'Owner', 'admin' => 'Admin', 'kasir' => 'Kasir', 'karyawan' => 'Karyawan', 'mekanik' => 'Mekanik'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('role') == $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Foto</label>
                    <input type="file" name="foto" class="form-control" accept="image/*">
                </div>

                <div class="col-12 d-flex gap-2 mt-2">
                    <button class="btn btn-warning text-white fw-semibold rounded-pill" type="submit"><i class="bi bi-check-lg-circle"></i> Simpan</button>
                    <a href="/pengguna" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div>
    </div>

@endsection
