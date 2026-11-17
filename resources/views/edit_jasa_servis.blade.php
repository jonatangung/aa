@extends('template')

@section('content')
{{-- Halaman form edit jasa servis. Data $row dikirim dari JasaServisController method editJasaServis(). Data dikirim ke method updateJasaServis() --}}

    <div class="card shadow-sm border-0 border-top border-warning border-3">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">Edit Jasa Servis</h5>
        </div>

        <div class="card-body">
            <form action="/edit_jasa_servis_aksi/{{ $row->id }}" method="POST" class="row g-3">
                @csrf

                <div class="col-md-4">
                    <label class="form-label">Kode Jasa</label>
                    <input type="text" name="kode_jasa" value="{{ $row->kode_jasa }}" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Nama Jasa</label>
                    <input type="text" name="nama_jasa" value="{{ $row->nama_jasa }}" class="form-control" required>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Harga</label>
                    <input type="number" name="harga" value="{{ $row->harga }}" class="form-control" min="0" required>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Estimasi (mnt)</label>
                    <input type="number" name="estimasi_menit" value="{{ $row->estimasi_menit }}" class="form-control" min="0">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3">{{ $row->deskripsi }}</textarea>
                </div>

                <div class="col-12 d-flex gap-2 mt-2">
                    <button class="btn btn-warning rounded-pill" type="submit"><i class="bi bi-check-lg-circle"></i> Perbarui</button>
                    <a href="/jasa-servis" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div>
    </div>

@endsection
