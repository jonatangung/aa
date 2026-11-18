{{-- Template utama: gabungkan header, sidebar (menu), isi halaman (content), dan script --}}
@include('header')

@include('menu')

<main id="main" class="main">
    @unless (request()->is('home'))
        <div class="no-print mb-3">
            <a href="/home" class="btn btn-warning text-dark fw-semibold rounded-pill btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>
    @endunless
    @yield('content')
</main>

@include('footer')
