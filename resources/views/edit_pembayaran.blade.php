@extends('template')

@section('content')
{{-- Halaman form edit pembayaran. Data $row, $servis, $detailJasa dikirim dari PembayaranController method editPembayaran(). Data dikirim ke method updatePembayaran() --}}

    <h1 class="mb-4 fw-bold">Edit Pembayaran</h1>

    <div class="card">
        <div class="card-body">
            <form action="/edit_pembayaran_aksi/{{ $row->id }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="fw-semibold">No Pembayaran</label>
                    <input type="text" name="no_pembayaran" value="{{ $row->no_pembayaran }}" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">Servis</label>
                    <select name="servis_id" class="form-control">
                        {{-- $servis = kumpulan data servis dari controller, $item = satu baris data di setiap putaran --}}
                        @foreach ($servis as $item)
                            <option value="{{ $item->id }}" @selected($row->servis_id == $item->id)>{{ $item->no_servis }} - {{ $item->keluhan }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">Tanggal</label>
                    <?php
                        //----- Tanggal dari database perlu diubah formatnya dulu ke Y-m-d untuk input date
                        $tanggal = '';
                        if ($row->tanggal != null && $row->tanggal != '') {
                            $tanggal = date('Y-m-d', strtotime($row->tanggal));
                        }
                    ?>
                    <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">Metode</label>
                    <select name="metode" class="form-control">
                        {{-- $row->metode = metode pembayaran tersimpan di database --}}
                        @foreach (['tunai' => 'Tunai', 'transfer' => 'Transfer', 'qris' => 'QRIS', 'debit' => 'Debit', 'kredit' => 'Kredit'] as $value => $label)
                            <option value="{{ $value }}" @selected($row->metode == $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <?php if ($detailJasa->isNotEmpty()) { ?>
                    {{-- Tabel rincian jasa yang dipilih servis ini --}}
                    <div class="mb-3">
                        <label class="fw-semibold">Jasa Servis yang Dipilih</label>
                        <table class="table table-sm table-bordered mb-1">
                            <thead class="table-light">
                                <tr>
                                    <th>Jasa</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-end">Harga</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- $detailJasa = daftar jasa servis ini, $jasa = satu baris jasa di setiap putaran --}}
                                @foreach ($detailJasa as $jasa)
                                    <tr>
                                        <td>{{ $jasa->nama_jasa }}</td>
                                        <td class="text-center">{{ $jasa->jumlah }}</td>
                                        <td class="text-end">Rp{{ number_format($jasa->harga, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp{{ number_format($jasa->jumlah * $jasa->harga, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <small class="text-muted">Jumlah bayar dihitung otomatis dari total jasa di atas. Ubah jasa melalui menu Servis.</small>
                    </div>
                <?php } ?>

                <div class="mb-3">
                    <label class="fw-semibold">Jumlah Bayar</label>
                    <input type="number" name="jumlah_bayar" value="{{ $row->jumlah_bayar }}" class="form-control" <?php if ($detailJasa->isNotEmpty()) { ?>readonly<?php } ?>>
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">No Referensi</label>
                    <input type="text" name="no_referensi" value="{{ $row->no_referensi }}" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="fw-semibold">Catatan</label>
                    <textarea name="catatan" class="form-control">{{ $row->catatan }}</textarea>
                </div>

                <button class="btn btn-warning" type="submit">Perbarui</button>
                <a href="/pembayaran" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>

@endsection
