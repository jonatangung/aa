{{-- Template utama: gabungkan header, sidebar (menu), isi halaman (content), dan footer --}}
@include('header')

@include('menu')

<main id="main" class="main">
    @yield('content')
</main>

@include('footer')
