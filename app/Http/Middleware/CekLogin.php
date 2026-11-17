<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CekLogin
{
    /**
     * Handle an incoming request.
     *
     * Middleware 'auth' untuk project ini: cek session('u') karena login
     * memakai session manual (bukan Auth bawaan Laravel).
     */
    //--- Fungsi: memastikan user sudah login sebelum boleh buka halaman
    //--- Dipakai di routes/web.php lewat alias 'auth' (didaftarkan di app/Http/Kernel.php)
    public function handle(Request $request, Closure $next)
    {
        //--- Cek apakah user sudah login (ada session('u') = nama user)
        if (!session('u')) {
            return redirect('/'); //--- Belum login, arahkan ke halaman login
        }

        return $next($request); //--- Sudah login, lanjutkan ke controller
    }
}
