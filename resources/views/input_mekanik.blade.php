@extends('template')

@section('content')
{{-- Halaman form tambah mekanik baru. Data dikirim ke MekanikController method aksi_tkry() --}}

    <div class="card shadow-sm border-0 border-top border-warning border-3">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">Tambah Mekanik</h5>
        </div>

        <div class="card-body">
            <form action="/aksi_tkry" method="POST" class="row g-3 no-print">
                @csrf

                <div class="col-12">
                    <label class="form-label">Nama Mekanik</label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
                </div>

                <div class="col-12">
                    <label class="form-label">No HP</label>
                    <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp') }}">
                </div>

                <div class="col-12">
                    <label class="form-label">Spesialisasi</label>
                    <input type="text" name="spesialisasi" class="form-control" value="{{ old('spesialisasi') }}">
                </div>

                <div class="col-12 d-flex gap-2 mt-2">
                    <button type="submit" class="btn btn-warning text-dark fw-semibold rounded-pill"><i class="bi bi-check-lg-circle"></i> Simpan</button>
                    <a href="/mekanik" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div>
    </div>

@endsection
