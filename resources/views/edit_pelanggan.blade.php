@extends('template')

@section('content')
{{-- Halaman form edit pelanggan. Data $row dikirim dari PelangganController method editPelanggan(). Data dikirim ke method updatePelanggan() --}}

    <div class="card shadow-sm border-0 border-top border-warning border-3">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">Edit Pelanggan</h5>
        </div>

        <div class="card-body">
            <form action="/edit_pelanggan_aksi/{{ $row->id }}" method="POST" class="row g-3">
                @csrf

                <div class="col-md-6">
                    <label class="form-label">Kode Pelanggan</label>
                    <input type="text" name="kode_pelanggan" value="{{ $row->kode_pelanggan }}" class="form-control">
                </div>

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
                    <input type="email" name="email" value="{{ $row->email }}" class="form-control">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Alamat</label>
                    <input type="text" name="alamat" value="{{ $row->alamat }}" class="form-control">
                </div>

                <div class="col-12 d-flex gap-2 mt-2">
                    <button class="btn btn-warning rounded-pill" type="submit"><i class="bi bi-check-lg-circle"></i> Perbarui</button>
                    <a href="/pelanggan" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div>
    </div>

@endsection
