@extends('template')

@section('content')
{{-- Halaman form tambah jasa servis baru. Data dikirim ke JasaServisController method simpanJasaServis() --}}

    <div class="card shadow-sm border-0 border-top border-warning border-3">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">Tambah Jasa Servis</h5>
        </div>

        <div class="card-body">
            <form action="/tambah_jasa_servis_aksi" method="POST" class="row g-3">
                @csrf

                <div class="col-12">
                    <label class="form-label">Nama Jasa</label>
                    <input type="text" name="nama_jasa" class="form-control" value="{{ old('nama_jasa') }}" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Harga</label>
                    <input type="number" name="harga" class="form-control" value="{{ old('harga') }}" min="0" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Estimasi Menit</label>
                    <input type="number" name="estimasi_menit" class="form-control" value="{{ old('estimasi_menit') }}" min="0">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi') }}</textarea>
                </div>

                <div class="col-12 d-flex gap-2 mt-2">
                    <button class="btn btn-warning text-white fw-semibold rounded-pill" type="submit"><i class="bi bi-check-lg-circle"></i> Simpan</button>
                    <a href="/jasa-servis" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div>
    </div>

@endsection
