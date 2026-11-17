@extends('template')

@section('content')
{{-- Halaman form edit transaksi servis. Data $row, $pelanggan, $kendaraan, $mekanik, $jasa, $detailJasa, $pembayaran dikirim dari ServisController method editServis(). Data dikirim ke method updateServis() --}}

    <div class="card shadow-sm border-0 border-top border-warning border-3">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">Edit Servis</h5>
        </div>

        <div class="card-body">
            <form action="/edit_servis_aksi/{{ $row->id }}" method="POST" class="row g-3">
                @csrf

                <div class="col-md-6">
                    <label class="form-label">No Servis</label>
                    <input type="text" name="no_servis" value="{{ $row->no_servis }}" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Pelanggan</label>
                    <select name="pelanggan_id" class="form-select">
                        {{-- $pelanggan = kumpulan data pelanggan dari controller, $item = satu baris data di setiap putaran --}}
                        @foreach ($pelanggan as $item)
                            <option value="{{ $item->id }}" @selected($row->pelanggan_id == $item->id)>{{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Kendaraan</label>
                    <select name="kendaraan_id" class="form-select">
                        {{-- $kendaraan = kumpulan data kendaraan dari controller, $item = satu baris data di setiap putaran --}}
                        @foreach ($kendaraan as $item)
                            <option value="{{ $item->id }}" @selected($row->kendaraan_id == $item->id)>{{ $item->no_polisi }} - {{ $item->merk }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Mekanik</label>
                    <select name="mekanik_id" class="form-select">
                        <option value="">-- Pilih Mekanik --</option>
                        {{-- $mekanik = kumpulan data mekanik dari controller, $item = satu baris data di setiap putaran --}}
                        @foreach ($mekanik as $item)
                            <option value="{{ $item->id }}" @selected($row->mekanik_id == $item->id)>{{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="antri" @selected($row->status == 'antri')>Antri</option>
                        <option value="dikerjakan" @selected($row->status == 'dikerjakan')>Dikerjakan</option>
                        <option value="selesai" @selected($row->status == 'selesai')>Selesai</option>
                        <option value="diambil" @selected($row->status == 'diambil')>Diambil</option>
                        <option value="batal" @selected($row->status == 'batal')>Batal</option>
                    </select>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Keluhan</label>
                    <textarea name="keluhan" class="form-control" rows="3">{{ $row->keluhan }}</textarea>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Jasa Servis</label>
                    <div class="border rounded p-3">
                        {{-- $jasa = semua jenis jasa, $detailJasa = jasa yang sudah dipilih servis ini --}}
                        @foreach ($jasa as $item)
                            <?php
                                //----- Cek apakah jasa ini sudah dipilih di servis ini
                                $selected = $detailJasa->firstWhere('jasa_servis_id', $item->id);
                                $jumlah = 1;
                                $terpilih = false;
                                if ($selected != null) {
                                    $terpilih = true;
                                    $jumlah = $selected->jumlah;
                                }
                            ?>
                            <div class="d-flex align-items-center gap-3 border rounded p-2 mb-2">
                                <div class="form-check mb-0">
                                    <input type="checkbox" name="jasa_servis_id[]" value="{{ $item->id }}" id="editJasa{{ $item->id }}" class="form-check-input" <?php if ($terpilih) { ?>checked<?php } ?>>
                                    <label for="editJasa{{ $item->id }}" class="form-check-label flex-grow-1">
                                        {{ $item->nama_jasa }} - Rp{{ number_format($item->harga, 0, ',', '.') }}
                                    </label>
                                </div>
                                <div class="col-2"><input type="number" name="jumlah_jasa[{{ $item->id }}]" value="{{ $jumlah }}" min="1" class="form-control" aria-label="Jumlah {{ $item->nama_jasa }}"></div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Metode Pembayaran</label>
                    <?php
                        //----- Ambil metode pembayaran servis ini, kosong berarti tunai
                        $metodePembayaran = 'tunai';
                        if ($pembayaran != null && $pembayaran->metode != '') {
                            $metodePembayaran = $pembayaran->metode;
                        }
                    ?>
                    <select name="metode_pembayaran" class="form-select" required>
                        <option value="tunai" @selected($metodePembayaran == 'tunai')>Tunai</option>
                        <option value="transfer" @selected($metodePembayaran == 'transfer')>Transfer</option>
                        <option value="qris" @selected($metodePembayaran == 'qris')>QRIS</option>
                        <option value="debit" @selected($metodePembayaran == 'debit')>Debit</option>
                        <option value="kredit" @selected($metodePembayaran == 'kredit')>Kredit</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control" rows="2">{{ $row->catatan }}</textarea>
                </div>

                <div class="col-12 d-flex gap-2 mt-2">
                    <button class="btn btn-warning rounded-pill" type="submit"><i class="bi bi-check-lg-circle"></i> Perbarui</button>
                    <a href="/servis" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div>
    </div>

@endsection
