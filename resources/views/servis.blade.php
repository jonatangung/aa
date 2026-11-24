@extends('template')

@section('content')
{{-- Halaman daftar transaksi servis. Data $rows dikirim dari ServisController method servis() --}}

    <?php
    //----- Warna badge untuk status servis
    $statusBadge = [
        'antri' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
        'dikerjakan' => 'bg-info-subtle text-info-emphasis border border-info-subtle',
        'selesai' => 'bg-success-subtle text-success-emphasis border border-success-subtle',
        'diambil' => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle',
        'batal' => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
    ];
    ?>

    <?php $canWrite = \App\Support\RolePermission::boleh('write:servis'); ?>
    <?php $canExport = \App\Support\RolePermission::boleh('export:servis'); ?>

    <div class="card shadow-sm border-0 border-top border-warning border-3">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="card-title mb-0">Transaksi Servis</h5>
            <div class="d-flex gap-2 flex-wrap no-print">
                <?php if ($canWrite) { ?>
                <button type="button" class="btn btn-warning text-white fw-semibold btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalTambahServis"><i class="bi bi-plus-lg-circle"></i> Tambah Servis</button>
                <?php } ?>
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
                <?php if ($canExport) { ?>
                <a href="/servis/pdf" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                <a href="/servis/excel" class="btn btn-outline-success btn-sm rounded-pill"><i class="bi bi-file-earmark-excel"></i> Excel</a>
                <?php } ?>
            </div>
        </div>

        <div class="card-body">
            {{-- Tabel daftar transaksi servis --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th>Pelanggan</th>
                            <th>Kendaraan</th>
                            <th>Jasa Servis</th>
                            <th class="text-end">Harga</th>
                            <th>Mekanik</th>
                            <th>Metode Pembayaran</th>
                            <th>Tanggal</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        {{-- $rows = kumpulan data servis dari controller, $value = satu baris data di setiap putaran --}}
                        @foreach ($rows as $value)
                            <tr>
                                <td class="text-center">{{ $no }}</td>
                                <?php $no++; ?>
                                <td>{{ $value->nama_pelanggan }}</td>
                                <td>{{ $value->no_polisi }}</td>
                                <td>{{ $value->daftar_jasa }}</td>
                                <td class="text-end">Rp{{ number_format($value->total_jasa, 0, ',', '.') }}</td>
                                <?php if ($value->nama_mekanik == '') { ?>
                                    <td>-</td>
                                <?php } else { ?>
                                    <td>{{ $value->nama_mekanik }}</td>
                                <?php } ?>
                                <?php if ($value->metode_pembayaran == '') { ?>
                                    <td>-</td>
                                <?php } else { ?>
                                    <td>{{ $value->metode_pembayaran }}</td>
                                <?php } ?>
                                <td>{{ $value->tanggal_masuk }}</td>
                                <td class="text-center"><span class="badge rounded-pill px-3 py-2 {{ $statusBadge[$value->status] ?? 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle' }}">{{ ucfirst($value->status) }}</span></td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1 align-items-center">
                                        <?php if ($canWrite) { ?>
                                        <a href="/edit_servis/{{ $value->id }}" class="btn btn-warning text-white fw-semibold btn-sm rounded-3" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                        <form action="/hapus_servis/{{ $value->id }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm rounded-3" title="Hapus"><i class="bi bi-trash"></i></button>
                                        </form>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php
    //----- Warna badge untuk pilihan status di form tambah
    $statusOpsi = [
        'antri' => 'Antri',
        'dikerjakan' => 'Dikerjakan',
        'selesai' => 'Selesai',
        'diambil' => 'Diambil',
        'batal' => 'Batal',
    ];
    ?>

    {{-- Modal Tambah Servis --}}
    <div class="modal fade modal-gold no-print" id="modalTambahServis" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-cart-check"></i> Tambah Servis</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/tambah_servis_aksi" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Pelanggan</label>
                                <select name="pelanggan_id" class="form-select" required>
                                    <option value="">-- Pilih Pelanggan --</option>
                                    @foreach ($pelanggan as $item)
                                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kendaraan</label>
                                <select name="kendaraan_id" class="form-select" required>
                                    <option value="">-- Pilih Kendaraan --</option>
                                    @foreach ($kendaraan as $item)
                                        <option value="{{ $item->id }}">{{ $item->no_polisi }} - {{ $item->merk }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mekanik</label>
                                <select name="mekanik_id" class="form-select">
                                    <option value="">-- Pilih Mekanik (Opsional) --</option>
                                    @foreach ($mekanik as $item)
                                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="" selected disabled>-- Pilih Status --</option>
                                    @foreach ($statusOpsi as $statusValue => $statusLabel)
                                        <option value="{{ $statusValue }}">{{ $statusLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Keluhan</label>
                                <textarea name="keluhan" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Jasa Servis</label>
                                <div class="border rounded p-3" style="border-color: rgba(212,175,55,0.35) !important;">
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
                            <div class="col-md-6">
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
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i> Batal</button>
                        <button type="submit" class="btn btn-warning text-white fw-semibold rounded-pill"><i class="bi bi-check-lg-circle"></i> Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
