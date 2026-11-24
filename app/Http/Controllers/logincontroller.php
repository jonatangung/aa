<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Support\RolePermission;

//----- LoginController: menangani halaman login, logout, registrasi, dan dashboard home
class LoginController extends Controller
{
    // ==================== LOGIN DAN DASHBOARD ====================

    //----- index(): Menampilkan halaman login
    public function index(){
        return view('login');
    }

    //----- aksilogin(): Memproses login — cek username/email & password, simpan session lalu redirect ke home
    public function aksilogin(Request $request){
        $validated = $request->validate([
            'u' => 'required|string|max:255',
            'p' => 'required|string|max:255',
        ]);

        $username = $validated['u'];
        $password = $validated['p'];

        // cari user di tabel users: cocokkan username dengan kolom nama ATAU email
        $hasilcek = DB::table('users')
            ->where('nama', $username) // cocokkan dengan kolom nama
            ->orWhere('email', $username) // atau cocokkan dengan kolom email
            ->first(); // ambil satu data pertama yang cocok

        // password baru disimpan sebagai hash bcrypt; password lama masih plaintext
        // jadi tetap bisa login, lalu otomatis di-hash ulang ke database
        $passwordValid = false;
        if ($hasilcek) {
            if (str_starts_with((string) $hasilcek->password, '$2y$')) {
                $passwordValid = Hash::check($password, $hasilcek->password);
            } else {
                $passwordValid = hash_equals((string) $hasilcek->password, $password);
                if ($passwordValid) {
                    DB::table('users')->where('id', $hasilcek->id)->update([
                        'password' => Hash::make($password),
                    ]);
                }
            }
        }

        if($passwordValid){
            DB::table('users')->where('id', $hasilcek->id)->update([
                'is_active' => 1,
                'updated_at' => now(),
            ]);

            if ($hasilcek->role === 'mekanik') {
                DB::table('mekanik')->where('user_id', $hasilcek->id)->update([
                    'status' => 'aktif',
                    'updated_at' => now(),
                ]);
            }

            session([
                'u' => $hasilcek->nama,
                'id_user' => $hasilcek->id,
                'role' => $hasilcek->role,
                'foto' => $hasilcek->foto,
            ]);
            return redirect('/home');
        }
        else{
            return redirect('/');
        }
    }

    //----- logout(): Menandai user nonaktif lalu menghapus semua session dan kembali ke halaman login
    public function logout(){
        if (session('id_user')) {
            DB::table('users')->where('id', session('id_user'))->update([
                'is_active' => 0,
                'updated_at' => now(),
            ]);

            if (session('role') === 'mekanik') {
                DB::table('mekanik')->where('user_id', session('id_user'))->update([
                    'status' => 'nonaktif',
                    'updated_at' => now(),
                ]);
            }
        }

        session()->flush();
        return redirect('/');
    }

    //----- register(): Menampilkan halaman registrasi — untuk guest yang belum login
    public function register()
    {
        return view('girasya');
    }

    //----- simpanRegister(): Menyimpan user baru dengan role default 'mekanik'
    public function simpanRegister(Request $request)
    {
        $validated = $request->validate([
            'u' => 'required|string|max:255',
            'e' => 'required|email|max:255|unique:users,email',
            'p' => 'required|string|min:6|max:255',
            'foto' => 'nullable|image|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
          $file = $request->file('foto');
          $filename = time() . '_' . $file->getClientOriginalName();
          $file->move(public_path('uploads'), $filename);
          $fotoPath = $filename;
        }

        DB::table('users')->insert([
            'nama' => $validated['u'],
            'email' => $validated['e'],
            'password' => Hash::make($validated['p']),
            'role' => 'tamu',
            'no_hp' => null,
            'foto' => $fotoPath,
            'is_active' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/login');
    }

    //----- home(): Menampilkan dashboard beserta jumlah pelanggan, kendaraan, mekanik, dan servis
    public function home(Request $request){
        // cek login sudah ditangani middleware 'auth' di routes/web.php, jadi langsung tampilkan view
        $role = session('role');

        //----- Statistik hanya dihitung untuk resource yang boleh dilihat role tsb (anti bocor data)
        $countIfCanView = function (string $resource, string $table) use ($role) {
            return RolePermission::boleh('view:' . $resource, $role)
                ? DB::table($table)->count()
                : null;
        };

        return view('home', [
            'pelanggan' => $countIfCanView('pelanggan', 'pelanggan'),
            'kendaraan' => $countIfCanView('kendaraan', 'kendaraan'),
            'mekanik' => $countIfCanView('mekanik', 'mekanik'),
            'servis' => $countIfCanView('servis', 'servis'),
        ]);
    }
}
