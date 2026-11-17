<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CekGuest
{
    /**
     * Handle an incoming request.
     *
     * Middleware 'guest' untuk project ini: user yang SUDAH login tidak
     * perlu buka halaman login/register lagi, langsung diarahkan ke home.
     */
    //--- Fungsi: memastikan hanya user yang BELUM login yang boleh buka halaman ini
    //--- Dipakai di routes/web.php lewat alias 'guest' (didaftarkan di app/Http/Kernel.php)
    public function handle(Request $request, Closure $next)
    {
        //--- Cek apakah user sudah login
        if (session('u')) {
            return redirect('/home'); //--- Sudah login, arahkan ke dashboard
        }

        return $next($request); //--- Belum login, lanjutkan ke halaman login/register
    }
}
