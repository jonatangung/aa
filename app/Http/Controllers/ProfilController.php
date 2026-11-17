<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

//----- ProfilController: menangani halaman dan update profil user yang sedang login
class ProfilController extends Controller
{
    // ==================== PROFIL ====================

    //----- profil(): Menampilkan halaman profil user yang sedang login
    public function profil()
    {
        //----- Cari user yang sedang login berdasarkan id di session
        $user = DB::table('users')->where('id', session('id_user'))->first();

        return view('profil', ['user' => $user]);
    }

    //----- updateProfil(): Memperbarui profil user sendiri (nama, email, no_hp, password opsional)
    public function updateProfil(Request $request)
    {
        $user = DB::table('users')->where('id', session('id_user'))->first();

        if (!$user) {
            return redirect('/profil');
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'no_hp' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6|max:255',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = [
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'],
            'updated_at' => now(),
        ];

        //----- Password hanya diganti kalau diisi (kosong = tidak berubah)
        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        //----- Upload foto baru ke folder public/uploads, foto lama dihapus
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);
            $data['foto'] = $filename;

            if ($user->foto && file_exists(public_path('uploads/' . $user->foto))) {
                unlink(public_path('uploads/' . $user->foto));
            }
        }

        //----- Simpan perubahan ke database
        DB::table('users')->where('id', $user->id)->update($data);

        //----- Ambil data terbaru lalu perbarui session supaya nama/foto di header ikut berubah
        $userUpdate = DB::table('users')->where('id', $user->id)->first();
        session([
            'u' => $validated['nama'],
            'foto' => $userUpdate->foto,
        ]);

        return redirect('/profil');
    }
}
