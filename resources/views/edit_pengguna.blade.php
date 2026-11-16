@extends('template')

@section('content')
{{-- Halaman form edit pengguna. Data $row dikirim dari PenggunaController method editPengguna(). Data dikirim ke method updatePengguna() --}}

    <h1 class="mb-4 fw-bold">Edit Pengguna</h1>

    <div class="card">
        <div class="card-body">
            <?php if ($errors->any()) { ?>
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            <?php } ?>

            <form action="/edit_users_aksi/{{ $row->id }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="fw-semibold">Nama</label>
                    <input type="text" name="nama" value="{{ $row->nama }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">Email</label>
                    <input type="email" name="email" value="{{ $row->email }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">Role</label>
                    <select name="role" class="form-control" required>
                        @foreach (['owner' => 'Owner', 'admin' => 'Admin', 'kasir' => 'Kasir', 'karyawan' => 'Karyawan', 'mekanik' => 'Mekanik'] as $value => $label)
                            <option value="{{ $value }}" @selected($row->role == $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">No HP</label>
                    <input type="text" name="no_hp" value="{{ $row->no_hp }}" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">Foto Profil</label>
                    <input type="file" name="foto" class="form-control" accept="image/*">
                    <?php if ($row->foto != null && $row->foto != '') { ?>
                        <img src="{{ asset('uploads/' . $row->foto) }}" width="100" class="mt-2 rounded">
                    <?php } ?>
                </div>

                <button class="btn btn-warning" type="submit">Perbarui</button>
                <a href="/pengguna" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>

@endsection
