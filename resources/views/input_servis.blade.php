@extends('template')

@section('content')
{{-- Halaman form tambah transaksi servis baru. Data $pelanggan, $kendaraan, $mekanik, $jasa dikirim dari ServisController method tambahServis(). Data dikirim ke method simpanServis() --}}

    <?php
    //----- Warna badge untuk pilihan status
    $statusBadge = [
        'antri' => 'bg-warning',
        'dikerjakan' => 'bg-info',
        'selesai' => 'bg-success',
        'diambil' => 'bg-primary',
        'batal' => 'bg-danger',
    ];
    ?>

    <div class="card shadow-sm border-0 border-top border-warning border-3">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">Tambah Servis</h5>
        </div>

        <div class="card-body">
            <form action="/tambah_servis_aksi" method="POST" class="row g-3">
                @csrf

                <div class="col-12">
                    <label class="form-label">Pelanggan</label>
                    <select name="pelanggan_id" class="form-select" required>
                        <option value="">-- Pilih Pelanggan --</option>
                        @foreach ($pelanggan as $item)
                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Kendaraan</label>
                    <select name="kendaraan_id" class="form-select" required>
                        <option value="">-- Pilih Kendaraan --</option>
                        @foreach ($kendaraan as $item)
                            <option value="{{ $item->id }}">{{ $item->no_polisi }} - {{ $item->merk }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Mekanik</label>
                    <select name="mekanik_id" class="form-select" required>
                        <option value="">-- Pilih Mekanik (Opsional) --</option>
                        @foreach ($mekanik as $item)
                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="" selected disabled>-- Pilih Status --</option>
                        @foreach ($statusBadge as $statusValue => $badgeClass)
                            <option value="{{ $statusValue }}">{{ ucfirst($statusValue) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Keluhan</label>
                    <textarea name="keluhan" class="form-control" rows="3" required></textarea>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Jasa Servis</label>
                    <div class="border rounded p-3">
                        @foreach ($jasa as $item)
                            <div class="d-flex align-items-center gap-3 border rounded p-2 mb-2">
                                <div class="form-check mb-0">
                                    <input type="checkbox" name="jasa_servis_id[]" value="{{ $item->id }}" id="jasa{{ $item->id }}" class="form-check-input">
                                    <label for="jasa{{ $item->id }}" class="form-check-label flex-grow-1">
                                        {{ $item->nama_jasa }} - Rp{{ number_format($item->harga, 0, ',', '.') }}
                                    </label>
                                </div>
                                <div class="col-2"><input type="number" name="jumlah_jasa[{{ $item->id }}]" value="1" min="1" class="form-control" aria-label="Jumlah {{ $item->nama_jasa }}"></div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label">Metode Pembayaran</label>
                    <select name="metode_pembayaran" class="form-select" required>
                        <option value="" disabled>-- Pilih Metode Pembayaran --</option>
                        <option value="tunai">Tunai</option>
                        <option value="transfer">Transfer</option>
                        <option value="qris">QRIS</option>
                        <option value="debit">Debit</option>
                        <option value="kredit">Kredit</option>
                    </select>
                </div>

                <div class="col-12 d-flex gap-2 mt-2">
                    <button class="btn btn-warning text-dark fw-semibold rounded-pill" type="submit"><i class="bi bi-check-lg-circle"></i> Simpan</button>
                    <a href="/servis" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div>
    </div>

@endsection
