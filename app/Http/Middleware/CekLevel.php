<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CekLevel
{
    /**
     * Handle an incoming request.
     *
     * Parameter route: ->middleware('level:owner,admin')
     * Memeriksa session('role') terhadap daftar level yang diizinkan.
     */
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (!session('u')) {
            return redirect('/'); //--- Belum login, arahkan ke halaman login
        }

        if (!in_array(session('role'), $roles, true)) {
            return redirect('/home'); //--- Level tidak sesuai
        }

        return $next($request);
    }
}
