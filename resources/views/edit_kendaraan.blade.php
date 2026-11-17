@extends('template')

@section('content')
{{-- Halaman form edit kendaraan. Data $row dan $pelanggan dikirim dari KendaraanController method editKendaraan(). Data dikirim ke method updateKendaraan() --}}

    <div class="card shadow-sm border-0 border-top border-warning border-3">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">Edit Kendaraan</h5>
        </div>

        <div class="card-body">
            <form action="/edit_kendaraan_aksi/{{ $row->id }}" method="POST" class="row g-3">
                @csrf

                <div class="col-md-6">
                    <label class="form-label">Pelanggan</label>
                    <select name="pelanggan_id" class="form-select" required>
                        {{-- $pelanggan = kumpulan data pelanggan dari controller, $item = satu baris data di setiap putaran --}}
                        @foreach ($pelanggan as $item)
                            <option value="{{ $item->id }}" @selected($row->pelanggan_id == $item->id)>
                                {{ $item->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">No Polisi</label>
                    <input type="text" name="no_polisi" value="{{ $row->no_polisi }}" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Merk</label>
                    <input type="text" name="merk" value="{{ $row->merk }}" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Model</label>
                    <input type="text" name="model" value="{{ $row->model }}" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Tahun</label>
                    <input type="number" name="tahun" value="{{ $row->tahun }}" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Warna</label>
                    <input type="text" name="warna" value="{{ $row->warna }}" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">No Rangka</label>
                    <input type="text" name="no_rangka" value="{{ $row->no_rangka }}" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">No Mesin</label>
                    <input type="text" name="no_mesin" value="{{ $row->no_mesin }}" class="form-control">
                </div>

                <div class="col-12 d-flex gap-2 mt-2">
                    <button class="btn btn-warning rounded-pill" type="submit"><i class="bi bi-check-lg-circle"></i> Perbarui</button>
                    <a href="/kendaraan" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div>
    </div>

@endsection
