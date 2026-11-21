{{-- Template utama: gabungkan header, sidebar (menu), isi halaman (content), dan script --}}
@include('header')

@include('menu')

<main id="main" class="main">
    @unless (request()->is('home'))
        <div class="no-print mb-3">
            <a href="@yield('back_url', '/home')" class="btn btn-warning text-dark fw-semibold rounded-pill btn-sm">
                <i class="bi bi-arrow-left"></i> @yield('back_text', 'Kembali ke Dashboard')
            </a>
        </div>
    @endunless
    @yield('content')
</main>

<footer id="footer" class="footer">
    <div class="copyright">
        &copy; Copyright <strong><span>NiceAdmin</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
        Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
    </div>
</footer>

@include('footer')
