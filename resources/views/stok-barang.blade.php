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
                <a href="/input_stok_barang" class="btn btn-warning text-dark fw-semibold btn-sm rounded-pill"><i class="bi bi-boxes"></i> Kelola Stok</a>
                <button type="button" class="btn btn-primary text-white fw-semibold btn-sm rounded-pill" onclick="generateQRCode()"><i class="bi bi-qr-code"></i> Generate QR Data Masuk</button>
                <?php } ?>
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
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
                    <div id="qrCodeInfo" class="mt-3 text-muted small" style="display: none;">
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

@endsection
