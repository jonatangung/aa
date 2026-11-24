{{-- Template utama: gabungkan header, sidebar (menu), isi halaman (content), dan script --}}
@include('header')

@include('menu')

<main id="main" class="main">
    @unless (request()->is('home'))
        <div class="no-print mb-3">
            <a href="/home" class="btn btn-warning text-white fw-semibold rounded-pill btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>
    @endunless
    @yield('content')
</main>

{{-- Modal notifikasi hasil simpan: muncul otomatis setelah redirect dari POST (sukses/gagal/validasi) --}}
@php
    $notifSukses = session('sukses');
    $notifGagal = session('gagal');
    $notifError = $errors->any() ? $errors->first() : null;
@endphp
@if ($notifSukses || $notifGagal || $notifError)
<div class="modal fade {{ $notifSukses ? 'modal-gold' : 'modal-gagal' }} no-print" id="modalNotif" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content text-center">
            <div class="modal-body py-4">
                @if ($notifSukses)
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 mb-1" style="color: #fbbf24; font-weight: 800;">Berhasil</h5>
                    <p class="text-white-50 mb-0">{{ $notifSukses }}</p>
                @else
                    <i class="bi bi-x-octagon-fill text-danger" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 mb-1" style="color: #f87171; font-weight: 800;">Gagal</h5>
                    <p class="text-white-50 mb-0">{{ $notifGagal ?: $notifError }}</p>
                @endif
            </div>
            <div class="modal-footer justify-content-center border-0">
                <button type="button" class="btn {{ $notifSukses ? 'btn-warning text-white' : 'btn-danger' }} fw-semibold rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        new bootstrap.Modal(document.getElementById('modalNotif')).show();
    });
</script>
@endif

@include('footer')
