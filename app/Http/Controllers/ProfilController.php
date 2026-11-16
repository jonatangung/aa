<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

//----- ProfilController: menangani halaman dan update profil user yang sedang login
class ProfilController extends Controller
{
    // ==================== PROFIL ====================

    //----- profil(): Menampilkan halaman profil user yang sedang login
    public function profil()
    {
        if (!session('u')) return redirect('/');

        $user = DB::table('users')->where('nama', session('u'))->first();

        return view('profil', ['user' => $user]);
    }

    //----- updateProfil(): Memperbarui profil user sendiri (nama, email, no_hp, password opsional)
    public function updateProfil(Request $request)
    {
        if (!session('u')) return redirect('/');

        $user = DB::table('users')->where('nama', session('u'))->first();

        if (!$user) {
            return redirect('/profil');
        }

        $data = [
            'nama' => $request->nama,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'updated_at' => now(),
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);
            $data['foto'] = $filename;

            if ($user->foto && file_exists(public_path('uploads/' . $user->foto))) {
                unlink(public_path('uploads/' . $user->foto));
            }
        }

        DB::table('users')->where('id', $user->id)->update($data);

        $userUpdate = DB::table('users')->where('id', $user->id)->first();
        session([
            'u' => $request->nama,
            'foto' => $userUpdate->foto,
        ]);

        return redirect('/profil');
    }
}
