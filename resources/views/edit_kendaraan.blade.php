@extends('template')

@section('content')
{{-- Halaman form edit kendaraan. Data $row dan $pelanggan dikirim dari KendaraanController method editKendaraan(). Data dikirim ke method updateKendaraan() --}}

    <h1 class="mb-4 fw-bold">Edit Kendaraan</h1>

    <div class="card">
        <div class="card-body">
            <form action="/edit_kendaraan_aksi/{{ $row->id }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="fw-semibold">Pelanggan</label>
                    <select name="pelanggan_id" class="form-control" required>
                        {{-- $pelanggan = kumpulan data pelanggan dari controller, $item = satu baris data di setiap putaran --}}
                        @foreach ($pelanggan as $item)
                            <?php if ($row->pelanggan_id == $item->id) { ?>
                                <option value="{{ $item->id }}" selected>
                                    {{ $item->nama }}
                                </option>
                            <?php } else { ?>
                                <option value="{{ $item->id }}">
                                    {{ $item->nama }}
                                </option>
                            <?php } ?>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">No Polisi</label>
                    <input type="text" name="no_polisi" value="{{ $row->no_polisi }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">Merk</label>
                    <input type="text" name="merk" value="{{ $row->merk }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">Model</label>
                    <input type="text" name="model" value="{{ $row->model }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">Tahun</label>
                    <input type="number" name="tahun" value="{{ $row->tahun }}" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">Warna</label>
                    <input type="text" name="warna" value="{{ $row->warna }}" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">No Rangka</label>
                    <input type="text" name="no_rangka" value="{{ $row->no_rangka }}" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">No Mesin</label>
                    <input type="text" name="no_mesin" value="{{ $row->no_mesin }}" class="form-control">
                </div>

                <button class="btn btn-warning" type="submit">Perbarui</button>
                <a href="/kendaraan" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>

@endsection
