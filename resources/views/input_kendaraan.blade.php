@extends('template')

@section('back_url', '/kendaraan')
@section('back_text', 'Kembali')

@section('content')
{{-- Halaman form tambah kendaraan baru. Data $pelanggan dikirim dari KendaraanController method tambahKendaraan(). Data dikirim ke method simpanKendaraan() --}}

    <div class="card shadow-sm border-0 border-top border-warning border-3">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">Tambah Kendaraan</h5>
        </div>

        <div class="card-body">
            <form action="/tambah_kendaraan_aksi" method="POST" class="row g-3">
                @csrf

                <div class="col-12">
                    <label class="form-label">Pelanggan</label>
                    <select name="pelanggan_id" class="form-select" required>
                        <option value="">-- Pilih Pelanggan --</option>
                        {{-- $pelanggan = kumpulan data pelanggan dari controller, $item = satu baris data di setiap putaran --}}
                        @foreach ($pelanggan as $item)
                            <option value="{{ $item->id }}" @selected(old('pelanggan_id') == $item->id)>
                                {{ $item->kode_pelanggan }} - {{ $item->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">No Polisi</label>
                    <input type="text" name="no_polisi" class="form-control" value="{{ old('no_polisi') }}" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Merk</label>
                    <input type="text" name="merk" class="form-control" value="{{ old('merk') }}" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Model</label>
                    <input type="text" name="model" class="form-control" value="{{ old('model') }}" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Tahun</label>
                    <input type="number" name="tahun" class="form-control" value="{{ old('tahun') }}">
                </div>

                <div class="col-12">
                    <label class="form-label">Warna</label>
                    <input type="text" name="warna" class="form-control" value="{{ old('warna') }}">
                </div>

                <div class="col-12">
                    <label class="form-label">No Rangka</label>
                    <input type="text" name="no_rangka" class="form-control" value="{{ old('no_rangka') }}">
                </div>

                <div class="col-12">
                    <label class="form-label">No Mesin</label>
                    <input type="text" name="no_mesin" class="form-control" value="{{ old('no_mesin') }}">
                </div>

                <div class="col-12 d-flex gap-2 mt-2">
                    <button type="submit" class="btn btn-warning text-dark fw-semibold rounded-pill"><i class="bi bi-check-lg-circle"></i> Simpan</button>
                    <a href="/kendaraan" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div>
    </div>

@endsection
