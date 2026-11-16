@extends('template')

@section('content')
{{-- Halaman form tambah kendaraan baru. Data $pelanggan dikirim dari KendaraanController method tambahKendaraan(). Data dikirim ke method simpanKendaraan() --}}

    <h1 class="mb-4 fw-bold">Tambah Kendaraan</h1>

    <div class="card">
        <div class="card-body">
            <form action="/tambah_kendaraan_aksi" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="fw-semibold">Pelanggan</label>
                    <select name="pelanggan_id" class="form-control" required>
                        <option value="">-- Pilih Pelanggan --</option>
                        {{-- $pelanggan = kumpulan data pelanggan dari controller, $item = satu baris data di setiap putaran --}}
                        @foreach ($pelanggan as $item)
                            <?php
                            $oldValue = old('pelanggan_id');
                            if ($oldValue == $item->id) {
                                $selected = 'selected';
                            } else {
                                $selected = '';
                            }
                            ?>
                            <option value="{{ $item->id }}" <?php echo $selected; ?>>
                                {{ $item->kode_pelanggan }} - {{ $item->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">No Polisi</label>
                    <?php $oldNoPolisi = old('no_polisi'); ?>
                    <input type="text" name="no_polisi" class="form-control" value="{{ $oldNoPolisi }}" required>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">Merk</label>
                    <?php $oldMerk = old('merk'); ?>
                    <input type="text" name="merk" class="form-control" value="{{ $oldMerk }}" required>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">Model</label>
                    <?php $oldModel = old('model'); ?>
                    <input type="text" name="model" class="form-control" value="{{ $oldModel }}" required>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">Tahun</label>
                    <?php $oldTahun = old('tahun'); ?>
                    <input type="number" name="tahun" class="form-control" value="{{ $oldTahun }}">
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">Warna</label>
                    <?php $oldWarna = old('warna'); ?>
                    <input type="text" name="warna" class="form-control" value="{{ $oldWarna }}">
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">No Rangka</label>
                    <?php $oldNoRangka = old('no_rangka'); ?>
                    <input type="text" name="no_rangka" class="form-control" value="{{ $oldNoRangka }}">
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">No Mesin</label>
                    <?php $oldNoMesin = old('no_mesin'); ?>
                    <input type="text" name="no_mesin" class="form-control" value="{{ $oldNoMesin }}">
                </div>

                <button type="submit" class="btn btn-primary px-4">Simpan</button>
                <a href="/kendaraan" class="btn btn-secondary px-4">Kembali</a>
            </form>
        </div>
    </div>

@endsection
