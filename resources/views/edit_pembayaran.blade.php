@extends('template')

@section('content')
{{-- Halaman form edit pembayaran. Data $row, $servis, $detailJasa dikirim dari PembayaranController method editPembayaran(). Data dikirim ke method updatePembayaran() --}}

    <div class="card shadow-sm border-0 border-top border-warning border-3">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">Edit Pembayaran</h5>
        </div>

        <div class="card-body">
            <form action="/edit_pembayaran_aksi/{{ $row->id }}" method="POST" class="row g-3">
                @csrf

                <div class="col-md-6">
                    <label class="form-label">No Pembayaran</label>
                    <input type="text" name="no_pembayaran" value="{{ $row->no_pembayaran }}" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Servis</label>
                    <select name="servis_id" class="form-select">
                        {{-- $servis = kumpulan data servis dari controller, $item = satu baris data di setiap putaran --}}
                        @foreach ($servis as $item)
                            <option value="{{ $item->id }}" @selected($row->servis_id == $item->id)>{{ $item->no_servis }} - {{ $item->keluhan }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Tanggal</label>
                    <?php
                        //----- Tanggal dari database perlu diubah formatnya dulu ke Y-m-d untuk input date
                        $tanggal = '';
                        if ($row->tanggal != null && $row->tanggal != '') {
                            $tanggal = date('Y-m-d', strtotime($row->tanggal));
                        }
                    ?>
                    <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Metode</label>
                    <select name="metode" class="form-select">
                        {{-- $row->metode = metode pembayaran tersimpan di database --}}
                        @foreach (['tunai' => 'Tunai', 'transfer' => 'Transfer', 'qris' => 'QRIS', 'debit' => 'Debit', 'kredit' => 'Kredit'] as $value => $label)
                            <option value="{{ $value }}" @selected($row->metode == $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Jumlah Bayar</label>
                    <input type="number" name="jumlah_bayar" value="{{ $row->jumlah_bayar }}" class="form-control" <?php if ($detailJasa->isNotEmpty()) { ?>readonly<?php } ?>>
                </div>

                <?php if ($detailJasa->isNotEmpty()) { ?>
                    {{-- Tabel rincian jasa yang dipilih servis ini --}}
                    <div class="col-md-12">
                        <label class="form-label">Jasa Servis yang Dipilih</label>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle">
                                <thead class="table-warning">
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
                        </div>
                        <small class="text-white-50">Jumlah bayar dihitung otomatis dari total jasa di atas. Ubah jasa melalui menu Servis.</small>
                    </div>
                <?php } ?>

                <div class="col-md-6">
                    <label class="form-label">No Referensi</label>
                    <input type="text" name="no_referensi" value="{{ $row->no_referensi }}" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control" rows="2">{{ $row->catatan }}</textarea>
                </div>

                <div class="col-12 d-flex gap-2 mt-2">
                    <button class="btn btn-warning rounded-pill" type="submit"><i class="bi bi-check-lg-circle"></i> Perbarui</button>
                    <a href="/pembayaran" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div>
    </div>

@endsection
