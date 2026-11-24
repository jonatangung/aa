@extends('template')

@section('content')
{{-- Halaman dashboard. Kartu menu mengikuti sidebar (menu.blade.php) dan hak akses role (App\Support\RolePermission) --}}

    <?php
        $role = session('role');
        $canView = fn (string $resource) => \App\Support\RolePermission::boleh('view:' . $resource, $role);
    ?>

    {{-- Card judul dashboard --}}
    <div class="dashboard-title-card card shadow-sm border-0 mb-3 no-print">
        <div class="card-body py-3 px-4 d-flex align-items-center">
            <div class="dashboard-title-card__icon me-3">
                <i class="bi bi-speedometer2"></i>
            </div>
            <div>
                <h1 class="dashboard-title-card__text mb-0">Dashboard Bengkel Pintar</h1>
                <small class="text-white-50">Panel kendali operasional bengkel</small>
            </div>
        </div>
    </div>

    {{-- Card sapaan selamat datang --}}
    <div class="dashboard-welcome-card card shadow-sm border-0 mb-4 no-print">
        <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center">
                <div class="dashboard-welcome-card__icon me-3">
                    <i class="bi bi-emoji-smile"></i>
                </div>
                <div>
                    <span class="text-white fw-semibold">Selamat datang, {{ session('u') }}</span>
                    <span class="badge bg-warning text-dark ms-2 rounded-pill">{{ $role }}</span>
                </div>
            </div>
            <small class="text-white-50"><i class="bi bi-calendar3 me-1"></i>{{ now()->locale('id')->translatedFormat('l, d F Y') }}</small>
        </div>
    </div>

    {{-- Card ajakan khusus tamu: mengajak pengunjung mendukung bisnis bengkel --}}
    <?php if ($role === 'tamu') { ?>
    <div class="guest-cta-card card shadow-sm border-0 mb-4 no-print">
        <div class="card-body p-4">
            <div class="d-flex align-items-start">
                <div class="guest-cta-card__icon me-3">
                    <i class="bi bi-heart-fill"></i>
                </div>
                <div class="flex-grow-1">
                    <h5 class="mb-2 fw-bold">
                        <i class="bi bi-stars me-1 text-warning"></i>Dukung Bengkel Pintar
                    </h5>
                    <p class="text-white-50 mb-2">
                        Setiap servis yang Anda percayakan adalah bentuk dukungan nyata bagi bengkel lokal kami.
                        Kami berkomitmen memberikan perawatan kendaraan yang jujur, berkualitas, dan transparan —
                        karena kepercayaan Anda adalah modal terbesar kami.
                    </p>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge rounded-pill px-3 py-2" style="background: rgba(16,185,129,.15); color:#34d399; border:1px solid rgba(16,185,129,.3);">
                            <i class="bi bi-shield-check me-1"></i>Bergaransi
                        </span>
                        <span class="badge rounded-pill px-3 py-2" style="background: rgba(59,130,246,.15); color:#60a5fa; border:1px solid rgba(59,130,246,.3);">
                            <i class="bi bi-tag me-1"></i>Harga Transparan
                        </span>
                        <span class="badge rounded-pill px-3 py-2" style="background: rgba(245,158,11,.15); color:#fbbf24; border:1px solid rgba(245,158,11,.3);">
                            <i class="bi bi-lightning-charge me-1"></i>Pengerjaan Cepat
                        </span>
                    </div>
                    <a href="/laporan-servis" class="btn btn-warning text-dark fw-semibold rounded-pill px-4 py-2">
                        <i class="bi bi-search me-1"></i>Cek Status Servis Anda
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>

    {{-- Ringkasan Statistik KPI --}}
    <?php if (collect($kpi)->contains(fn ($v) => $v !== null)) { ?>
    <div class="row no-print mb-4">
        <?php if ($kpi['pendapatan_hari_ini'] !== null) { ?>
        <div class="col-6 col-xl-3 mb-3">
            <div class="card kpi-card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="kpi-icon kpi-icon-gold">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div class="ms-3 overflow-hidden">
                        <small class="text-white-50 d-block text-truncate">Pendapatan Hari Ini</small>
                        <h4 class="mb-0 text-white fw-bold">Rp {{ number_format($kpi['pendapatan_hari_ini'], 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if ($kpi['pendapatan_bulan_ini'] !== null) { ?>
        <div class="col-6 col-xl-3 mb-3">
            <div class="card kpi-card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="kpi-icon kpi-icon-green">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div class="ms-3 overflow-hidden">
                        <small class="text-white-50 d-block text-truncate">Pendapatan Bulan Ini</small>
                        <h4 class="mb-0 text-white fw-bold">Rp {{ number_format($kpi['pendapatan_bulan_ini'], 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if ($kpi['servis_aktif'] !== null) { ?>
        <div class="col-6 col-xl-3 mb-3">
            <div class="card kpi-card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="kpi-icon kpi-icon-blue">
                        <i class="bi bi-clipboard2-pulse"></i>
                    </div>
                    <div class="ms-3 overflow-hidden">
                        <small class="text-white-50 d-block text-truncate">Servis Aktif</small>
                        <h4 class="mb-0 text-white fw-bold">{{ $kpi['servis_aktif'] }} Unit</h4>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if ($kpi['total_pelanggan'] !== null) { ?>
        <div class="col-6 col-xl-3 mb-3">
            <div class="card kpi-card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="kpi-icon kpi-icon-purple">
                        <i class="bi bi-person-check"></i>
                    </div>
                    <div class="ms-3 overflow-hidden">
                        <small class="text-white-50 d-block text-truncate">Total Pelanggan</small>
                        <h4 class="mb-0 text-white fw-bold">{{ $kpi['total_pelanggan'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
    <?php } ?>

    {{-- Chart Statistik (ApexCharts - NiceAdmin) --}}
    <?php
        $adaChart = collect($chartData)->contains(fn ($v) => $v !== null);
    ?>
    <?php if ($adaChart) { ?>
    <div class="row no-print mb-4">
        <?php if ($chartData['pendapatan_bulanan']) { ?>
        <div class="col-12 col-lg-8 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-graph-up me-2"></i>Pendapatan 6 Bulan Terakhir</h5>
                    <p class="text-white-50 small">Total pemasukan dari tabel pembayaran per bulan</p>
                    <div id="chartPendapatan" style="min-height: 300px;"></div>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if ($chartData['status_servis']) { ?>
        <div class="col-12 col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-pie-chart me-2"></i>Status Servis</h5>
                    <p class="text-white-50 small">Distribusi antrean kendaraan di bengkel</p>
                    <div id="chartStatusServis" style="min-height: 300px;"></div>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if ($chartData['servis_bulanan']) { ?>
        <div class="col-12 col-lg-6 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-bar-chart me-2"></i>Servis Masuk per Bulan</h5>
                    <p class="text-white-50 small">Jumlah transaksi servis 6 bulan terakhir</p>
                    <div id="chartServisBulanan" style="min-height: 280px;"></div>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if ($chartData['metode_pembayaran']) { ?>
        <div class="col-12 col-lg-6 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-credit-card me-2"></i>Pendapatan per Metode Bayar</h5>
                    <p class="text-white-50 small">Komposisi pemasukan berdasarkan metode pembayaran</p>
                    <div id="chartMetode" style="min-height: 280px;"></div>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if ($chartData['pelanggan_bulanan']) { ?>
        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-people me-2"></i>Pelanggan Baru per Bulan</h5>
                    <p class="text-white-50 small">Pertumbuhan pelanggan terdaftar 6 bulan terakhir</p>
                    <div id="chartPelangganBulanan" style="min-height: 280px;"></div>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
    <?php } ?>

    {{-- Kartu menu cepat, tiap kartu hanya tampil sesuai hak akses role user --}}
    <div class="row no-print mb-4">
        <?php if ($canView('pelanggan')) { ?>
        <div class="col-md-6 col-xl-3 mb-3">
            <a href="/pelanggan" class="text-decoration-none">
                <div class="card shadow-sm border-0 border-top border-warning border-3 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-person fs-1 text-warning"></i>
                        <h5 class="mt-2 text-white">Data Pelanggan</h5>
                        <p class="small text-white-50 mb-0">Kelola data pelanggan</p>
                    </div>
                </div>
            </a>
        </div>
        <?php } ?>

        <?php if ($canView('kendaraan')) { ?>
        <div class="col-md-6 col-xl-3 mb-3">
            <a href="/kendaraan" class="text-decoration-none">
                <div class="card shadow-sm border-0 border-top border-warning border-3 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-car-front fs-1 text-warning"></i>
                        <h5 class="mt-2 text-white">Data Kendaraan</h5>
                        <p class="small text-white-50 mb-0">Kelola data kendaraan</p>
                    </div>
                </div>
            </a>
        </div>
        <?php } ?>

        <?php if ($canView('mekanik')) { ?>
        <div class="col-md-6 col-xl-3 mb-3">
            <a href="/mekanik" class="text-decoration-none">
                <div class="card shadow-sm border-0 border-top border-warning border-3 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-person-badge fs-1 text-warning"></i>
                        <h5 class="mt-2 text-white">Data Mekanik</h5>
                        <p class="small text-white-50 mb-0">Kelola data Mekanik</p>
                    </div>
                </div>
            </a>
        </div>
        <?php } ?>

        <?php if ($canView('jasa_servis')) { ?>
        <div class="col-md-6 col-xl-3 mb-3">
            <a href="/jasa-servis" class="text-decoration-none">
                <div class="card shadow-sm border-0 border-top border-warning border-3 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-wrench-adjustable fs-1 text-warning"></i>
                        <h5 class="mt-2 text-white">Data Jasa Servis</h5>
                        <p class="small text-white-50 mb-0">Kelola jenis dan harga jasa</p>
                    </div>
                </div>
            </a>
        </div>
        <?php } ?>

        <?php if ($canView('supplier')) { ?>
        <div class="col-md-6 col-xl-3 mb-3">
            <a href="/supplier" class="text-decoration-none">
                <div class="card shadow-sm border-0 border-top border-warning border-3 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-truck fs-1 text-warning"></i>
                        <h5 class="mt-2 text-white">Data Supplier</h5>
                        <p class="small text-white-50 mb-0">Kelola data supplier</p>
                    </div>
                </div>
            </a>
        </div>
        <?php } ?>

        <?php if ($canView('servis')) { ?>
        <div class="col-md-6 col-xl-3 mb-3">
            <a href="/servis" class="text-decoration-none">
                <div class="card shadow-sm border-0 border-top border-warning border-3 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-cart-check fs-1 text-warning"></i>
                        <h5 class="mt-2 text-white">Transaksi Servis</h5>
                        <p class="small text-white-50 mb-0">Kelola transaksi bengkel</p>
                    </div>
                </div>
            </a>
        </div>
        <?php } ?>

        <?php if ($canView('pembayaran')) { ?>
        <div class="col-md-6 col-xl-3 mb-3">
            <a href="/pembayaran" class="text-decoration-none">
                <div class="card shadow-sm border-0 border-top border-warning border-3 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-cash-stack fs-1 text-warning"></i>
                        <h5 class="mt-2 text-white">Pembayaran</h5>
                        <p class="small text-white-50 mb-0">Kelola pembayaran servis</p>
                    </div>
                </div>
            </a>
        </div>
        <?php } ?>

        <?php if ($canView('laporan_servis')) { ?>
        <div class="col-md-6 col-xl-3 mb-3">
            <a href="/laporan-servis" class="text-decoration-none">
                <div class="card shadow-sm border-0 border-top border-warning border-3 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-file-earmark-text fs-1 text-warning"></i>
                        <h5 class="mt-2 text-white">Laporan Servis</h5>
                        <p class="small text-white-50 mb-0">Lihat laporan transaksi servis</p>
                    </div>
                </div>
            </a>
        </div>
        <?php } ?>

        <?php if ($canView('laporan_pendapatan')) { ?>
        <div class="col-md-6 col-xl-3 mb-3">
            <a href="/laporan-pendapatan" class="text-decoration-none">
                <div class="card shadow-sm border-0 border-top border-warning border-3 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-bar-chart fs-1 text-warning"></i>
                        <h5 class="mt-2 text-white">Laporan Pendapatan</h5>
                        <p class="small text-white-50 mb-0">Lihat laporan pendapatan</p>
                    </div>
                </div>
            </a>
        </div>
        <?php } ?>

        <?php if ($canView('stok_barang')) { ?>
        <div class="col-md-6 col-xl-3 mb-3">
            <a href="/stok-barang" class="text-decoration-none">
                <div class="card shadow-sm border-0 border-top border-warning border-3 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-box-seam fs-1 text-warning"></i>
                        <h5 class="mt-2 text-white">Stok Barang</h5>
                        <p class="small text-white-50 mb-0">Kelola stok barang</p>
                    </div>
                </div>
            </a>
        </div>
        <?php } ?>

        <?php if ($canView('pengguna')) { ?>
        <div class="col-md-6 col-xl-3 mb-3">
            <a href="/pengguna" class="text-decoration-none">
                <div class="card shadow-sm border-0 border-top border-warning border-3 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-people fs-1 text-warning"></i>
                        <h5 class="mt-2 text-white">Manajemen Pengguna</h5>
                        <p class="small text-white-50 mb-0">Kelola akun pengguna</p>
                    </div>
                </div>
            </a>
        </div>
        <?php } ?>

        <div class="col-md-6 col-xl-3 mb-3">
            <a href="/profil" class="text-decoration-none">
                <div class="card shadow-sm border-0 border-top border-warning border-3 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-gear fs-1 text-warning"></i>
                        <h5 class="mt-2 text-white">Pengaturan Profil</h5>
                        <p class="small text-white-50 mb-0">Kelola profil akun</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Inisialisasi chart ApexCharts --}}
    @if ($adaChart)
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartData = @json($chartData);
            const baseOptions = {
                chart: {
                    background: 'transparent',
                    foreColor: '#cbd5e1',
                    toolbar: { show: false },
                    fontFamily: 'Plus Jakarta Sans, sans-serif'
                },
                theme: { mode: 'dark' },
                tooltip: { theme: 'dark' }
            };
            const rupiah = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 });

            //----- Area chart: Pendapatan 6 bulan
            if (chartData.pendapatan_bulanan && document.querySelector('#chartPendapatan')) {
                new ApexCharts(document.querySelector('#chartPendapatan'), {
                    ...baseOptions,
                    chart: { ...baseOptions.chart, type: 'area', height: 300, zoom: { enabled: false } },
                    series: [{ name: 'Pendapatan', data: chartData.pendapatan_bulanan.series[0] }],
                    xaxis: { categories: chartData.pendapatan_bulanan.labels, axisBorder: { show: false }, axisTicks: { show: false } },
                    yaxis: { labels: { formatter: v => 'Rp ' + new Intl.NumberFormat('id-ID').format(v) } },
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 3, colors: ['#f59e0b'] },
                    fill: {
                        type: 'gradient',
                        gradient: { shadeIntensity: 1, opacityFrom: 0.45, opacityTo: 0.05, stops: [0, 90, 100] }
                    },
                    colors: ['#f59e0b']
                }).render();
            }

            //----- Donut: Status servis
            if (chartData.status_servis && document.querySelector('#chartStatusServis')) {
                new ApexCharts(document.querySelector('#chartStatusServis'), {
                    ...baseOptions,
                    chart: { ...baseOptions.chart, type: 'donut', height: 300 },
                    series: chartData.status_servis.series,
                    labels: chartData.status_servis.labels,
                    colors: ['#f59e0b', '#3b82f6', '#10b981', '#8b5cf6', '#ef4444'],
                    legend: { position: 'bottom', fontSize: '12px' },
                    dataLabels: { enabled: false },
                    plotOptions: { pie: { donut: { size: '65%' } } }
                }).render();
            }

            //----- Bar: Servis masuk per bulan
            if (chartData.servis_bulanan && document.querySelector('#chartServisBulanan')) {
                new ApexCharts(document.querySelector('#chartServisBulanan'), {
                    ...baseOptions,
                    chart: { ...baseOptions.chart, type: 'bar', height: 280, toolbar: { show: false } },
                    series: [{ name: 'Servis', data: chartData.servis_bulanan.series[0] }],
                    xaxis: { categories: chartData.servis_bulanan.labels, axisBorder: { show: false }, axisTicks: { show: false } },
                    yaxis: { labels: { formatter: v => Math.round(v) }, forceNiceScale: true },
                    dataLabels: { enabled: false },
                    plotOptions: { bar: { borderRadius: 6, columnWidth: '55%' } },
                    colors: ['#22d3ee'],
                    fill: { type: 'gradient', gradient: { shade: 'light', type: 'vertical', opacityFrom: 0.9, opacityTo: 0.4 } }
                }).render();
            }

            //----- Donut: Pendapatan per metode pembayaran
            if (chartData.metode_pembayaran && document.querySelector('#chartMetode')) {
                new ApexCharts(document.querySelector('#chartMetode'), {
                    ...baseOptions,
                    chart: { ...baseOptions.chart, type: 'donut', height: 280 },
                    series: chartData.metode_pembayaran.series,
                    labels: chartData.metode_pembayaran.labels,
                    colors: ['#10b981', '#3b82f6', '#8b5cf6', '#f97316', '#ef4444'],
                    legend: { position: 'bottom', fontSize: '12px' },
                    dataLabels: { enabled: false },
                    plotOptions: { pie: { donut: { size: '65%' } } },
                    tooltip: { y: { formatter: v => rupiah.format(v) } }
                }).render();
            }

            //----- Bar: Pelanggan baru per bulan
            if (chartData.pelanggan_bulanan && document.querySelector('#chartPelangganBulanan')) {
                new ApexCharts(document.querySelector('#chartPelangganBulanan'), {
                    ...baseOptions,
                    chart: { ...baseOptions.chart, type: 'bar', height: 280, toolbar: { show: false } },
                    series: [{ name: 'Pelanggan', data: chartData.pelanggan_bulanan.series[0] }],
                    xaxis: { categories: chartData.pelanggan_bulanan.labels, axisBorder: { show: false }, axisTicks: { show: false } },
                    yaxis: { labels: { formatter: v => Math.round(v) }, forceNiceScale: true },
                    dataLabels: { enabled: false },
                    plotOptions: { bar: { borderRadius: 6, columnWidth: '55%' } },
                    colors: ['#a855f7'],
                    fill: { type: 'gradient', gradient: { shade: 'light', type: 'vertical', opacityFrom: 0.9, opacityTo: 0.4 } }
                }).render();
            }
        });
    </script>
    @endif

@endsection
