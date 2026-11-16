@extends('template')

@section('content')
{{-- Halaman form edit transaksi servis. Data $row, $pelanggan, $kendaraan, $mekanik, $jasa, $detailJasa, $pembayaran dikirim dari ServisController method editServis(). Data dikirim ke method updateServis() --}}

    <h1 class="mb-4 fw-bold">Edit Servis</h1>

    <div class="card">
        <div class="card-body">
            <form action="/edit_servis_aksi/{{ $row->id }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="fw-semibold">No Servis</label>
                    <input type="text" name="no_servis" value="{{ $row->no_servis }}" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">Pelanggan</label>
                    <select name="pelanggan_id" class="form-control">
                        {{-- $pelanggan = kumpulan data pelanggan dari controller, $item = satu baris data di setiap putaran --}}
                        @foreach ($pelanggan as $item)
                            <?php if ($row->pelanggan_id == $item->id) { ?>
                                <option value="{{ $item->id }}" selected>{{ $item->nama }}</option>
                            <?php } else { ?>
                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                            <?php } ?>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">Kendaraan</label>
                    <select name="kendaraan_id" class="form-control">
                        {{-- $kendaraan = kumpulan data kendaraan dari controller, $item = satu baris data di setiap putaran --}}
                        @foreach ($kendaraan as $item)
                            <option value="{{ $item->id }}" @selected($row->kendaraan_id == $item->id)>{{ $item->no_polisi }} - {{ $item->merk }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">Mekanik</label>
                    <select name="mekanik_id" class="form-control">
                        <option value="">-- Pilih Mekanik --</option>
                        {{-- $mekanik = kumpulan data mekanik dari controller, $item = satu baris data di setiap putaran --}}
                        @foreach ($mekanik as $item)
                            <option value="{{ $item->id }}" @selected($row->mekanik_id == $item->id)>{{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">Keluhan</label>
                    <textarea name="keluhan" class="form-control">{{ $row->keluhan }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">Jasa Servis</label>
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
                        <div class="d-flex align-items-center gap-2 border rounded p-2 mb-2">
                            <input type="checkbox" name="jasa_servis_id[]" value="{{ $item->id }}" id="editJasa{{ $item->id }}" <?php if ($terpilih) { ?>checked<?php } ?>>
                            <label for="editJasa{{ $item->id }}" class="mb-0 flex-grow-1">
                                {{ $item->nama_jasa }} - Rp{{ number_format($item->harga, 0, ',', '.') }}
                            </label>
                            <input type="number" name="jumlah_jasa[{{ $item->id }}]" value="{{ $jumlah }}" min="1" class="form-control" style="width: 90px;">
                        </div>
                    @endforeach
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">Metode Pembayaran</label>
                    <?php
                        //----- Ambil metode pembayaran servis ini, kosong berarti tunai
                        $metodePembayaran = 'tunai';
                        if ($pembayaran != null && $pembayaran->metode != '') {
                            $metodePembayaran = $pembayaran->metode;
                        }
                    ?>
                    <select name="metode_pembayaran" class="form-control" required>
                        <option value="tunai" @selected($metodePembayaran == 'tunai')>Tunai</option>
                        <option value="transfer" @selected($metodePembayaran == 'transfer')>Transfer</option>
                        <option value="qris" @selected($metodePembayaran == 'qris')>QRIS</option>
                        <option value="debit" @selected($metodePembayaran == 'debit')>Debit</option>
                        <option value="kredit" @selected($metodePembayaran == 'kredit')>Kredit</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">Status</label>
                    <select name="status" class="form-control">
                        <option value="antri" @selected($row->status == 'antri')>Antri</option>
                        <option value="dikerjakan" @selected($row->status == 'dikerjakan')>Dikerjakan</option>
                        <option value="selesai" @selected($row->status == 'selesai')>Selesai</option>
                        <option value="diambil" @selected($row->status == 'diambil')>Diambil</option>
                        <option value="batal" @selected($row->status == 'batal')>Batal</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">Catatan</label>
                    <textarea name="catatan" class="form-control">{{ $row->catatan }}</textarea>
                </div>

                <button class="btn btn-warning" type="submit">Perbarui</button>
                <a href="/servis" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>

@endsection
