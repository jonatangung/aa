@extends('template')

@section('content')
{{-- Halaman rekap stok barang. Data $rows dikirim dari StokBarangController method stokBarang() --}}

    <?php $canWrite = \App\Support\RolePermission::boleh('write:stok_barang'); ?>
    <?php $canExport = \App\Support\RolePermission::boleh('export:stok_barang'); ?>

    <div class="card shadow-sm border-0 border-top border-warning border-3">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="card-title mb-0">Stok Barang</h5>
            <div class="d-flex gap-2 flex-wrap no-print">
                <?php if ($canWrite) { ?>
                <button type="button" class="btn btn-warning text-white fw-semibold btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalKelolaStok"><i class="bi bi-boxes"></i> Kelola Stok</button>
                <button type="button" class="btn btn-primary text-white fw-semibold btn-sm rounded-pill" onclick="generateQRCode()"><i class="bi bi-qr-code"></i> Generate QR Data Masuk</button>
                <?php } ?>
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
                <?php if ($canExport) { ?>
                <a href="/stok-barang/pdf" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                <a href="/stok-barang/excel" class="btn btn-outline-success btn-sm rounded-pill"><i class="bi bi-file-earmark-excel"></i> Excel</a>
                <?php } ?>
            </div>
        </div>

        <div class="card-body">
            {{-- Tabel rekap stok per barang --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th>Supplier</th>
                            <th>Nama Barang</th>
                            <th class="text-end">Harga</th>
                            <th>Stok Sekarang</th>
                            <th>Stok Masuk</th>
                            <th>Tanggal Masuk</th>
                            <th>Stok Keluar</th>
                            <th>Tanggal Keluar</th>
                            <th class="text-end">Harga Jual</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        {{-- $rows = rekap stok dari controller, $row = satu baris data di setiap putaran --}}
                        @foreach ($rows as $row)
                            <tr>
                                <td class="text-center">{{ $no }}</td>
                                <?php $no++; ?>
                                <td>{{ $row->nama_supplier }}</td>
                                <?php if ($row->nama_barang == null || $row->nama_barang == '') { ?>
                                    <td>-</td>
                                <?php } else { ?>
                                    <td>{{ $row->nama_barang }}</td>
                                <?php } ?>
                                <td class="text-end">Rp{{ number_format($row->harga, 0, ',', '.') }}</td>
                                <td>{{ $row->stok }}</td>
                                <td>{{ $row->stok_masuk }}</td>
                                <td>{{ $row->tanggal_masuk }}</td>
                                <td>{{ $row->stok_keluar }}</td>
                                <td>{{ $row->tanggal_keluar }}</td>
                                <td class="text-end">Rp{{ number_format($row->harga_jual, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal QR Code --}}
    <div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrCodeModalLabel">QR Code Data Masuk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="qrCodeContainer">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div id="qrCodeInfo" class="mt-3 text-white-50 small" style="display: none;">
                        <p>QR Code ini berlaku selama <strong>5 menit</strong>.</p>
                        <p>Kadaluarsa dalam: <span id="countdown" class="fw-bold text-danger">05:00</span></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    let countdownInterval;

    function generateQRCode() {
        const modal = new bootstrap.Modal(document.getElementById('qrCodeModal'));
        modal.show();

        document.getElementById('qrCodeContainer').innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
        document.getElementById('qrCodeInfo').style.display = 'none';

        // Hentikan countdown sebelumnya jika ada
        if (countdownInterval) {
            clearInterval(countdownInterval);
        }

        fetch('/stok-barang/generate-qr')
            .then(response => response.json())
            .then(data => {
                if (data.type === 'svg') {
                    document.getElementById('qrCodeContainer').innerHTML = '<div class="text-center">' + data.qr_code + '</div>';
                } else {
                    document.getElementById('qrCodeContainer').innerHTML = '<img src="data:image/png;base64,' + data.qr_code + '" alt="QR Code" class="img-fluid mx-auto d-block" style="max-width: 250px;">';
                }
                document.getElementById('qrCodeInfo').style.display = 'block';

                // Mulai countdown
                let secondsLeft = data.expires_in_seconds;
                updateCountdown(secondsLeft);

                countdownInterval = setInterval(() => {
                    secondsLeft--;
                    if (secondsLeft <= 0) {
                        clearInterval(countdownInterval);
                        document.getElementById('countdown').textContent = '00:00';
                        document.getElementById('countdown').classList.add('text-danger');
                    } else {
                        updateCountdown(secondsLeft);
                    }
                }, 1000);
            })
            .catch(error => {
                document.getElementById('qrCodeContainer').innerHTML = '<div class="alert alert-danger">Gagal generate QR Code. Silakan coba lagi.</div>';
            });
    }

    function updateCountdown(seconds) {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = seconds % 60;
        const formatted = String(minutes).padStart(2, '0') + ':' + String(remainingSeconds).padStart(2, '0');
        document.getElementById('countdown').textContent = formatted;
    }
    </script>

    {{-- Modal Kelola Stok Barang --}}
    <div class="modal fade modal-gold no-print" id="modalKelolaStok" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-boxes"></i> Kelola Stok Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formStokKeluar" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Pilih Barang</label>
                                <select id="pilihBarang" class="form-select" required>
                                    <option value="" disabled selected>-- Pilih Barang --</option>
                                    @foreach ($supplierData as $item)
                                        <?php
                                            $namaBarang = 'Tanpa Nama Barang';
                                            if ($item->nama_barang != null && $item->nama_barang != '') {
                                                $namaBarang = $item->nama_barang;
                                            }
                                        ?>
                                        <option value="{{ $item->id }}">{{ $item->nama_supplier }} - {{ $namaBarang }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Jumlah Keluar</label>
                                <input type="number" name="jumlah" min="1" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Tanggal Keluar</label>
                                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Harga Jual</label>
                                <input type="number" name="harga_jual" min="0" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i> Batal</button>
                        <button type="submit" id="btnSimpanStok" class="btn btn-danger rounded-pill" disabled><i class="bi bi-check-lg-circle"></i> Simpan Keluar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    //----- Arahkan form ke /stok-barang_keluar/{id} sesuai barang yang dipilih
    document.getElementById('pilihBarang').addEventListener('change', function () {
        var form = document.getElementById('formStokKeluar');
        var btn = document.getElementById('btnSimpanStok');
        if (this.value) {
            form.action = '/stok-barang_keluar/' + this.value;
            btn.disabled = false;
        } else {
            form.removeAttribute('action');
            btn.disabled = true;
        }
    });
    </script>

@endsection
