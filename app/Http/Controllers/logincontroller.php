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

        //----- Izin data untuk KPI & chart dashboard
        $bolehPendapatan = RolePermission::boleh('view:pembayaran', $role)
            || RolePermission::boleh('view:laporan_pendapatan', $role);
        $bolehServis = RolePermission::boleh('view:servis', $role);
        $bolehPelanggan = RolePermission::boleh('view:pelanggan', $role);

        //----- KPI ringkasan
        $kpi = [
            'pendapatan_hari_ini' => $bolehPendapatan
                ? (float) DB::table('pembayaran')->whereDate('tanggal', today())->sum('jumlah_bayar')
                : null,
            'pendapatan_bulan_ini' => $bolehPendapatan
                ? (float) DB::table('pembayaran')
                    ->whereYear('tanggal', now()->year)
                    ->whereMonth('tanggal', now()->month)
                    ->sum('jumlah_bayar')
                : null,
            'servis_aktif' => $bolehServis
                ? DB::table('servis')->whereIn('status', ['antri', 'dikerjakan'])->count()
                : null,
            'total_pelanggan' => $bolehPelanggan
                ? DB::table('pelanggan')->count()
                : null,
        ];

        //----- Label 6 bulan terakhir (format "Nov 25") untuk sumbu X chart
        $bulanLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulanLabels[] = now()->subMonths($i)->locale('id')->translatedFormat('M y');
        }

        //----- Helper: ubah hasil groupBy YYYY-MM menjadi array 6 bulan berurutan (bulan tanpa data = 0)
        $runtun6Bulan = function ($grouped) use ($bulanLabels) {
            $keys = [];
            for ($i = 5; $i >= 0; $i--) {
                $keys[] = now()->subMonths($i)->format('Y-m');
            }
            $data = [];
            foreach ($keys as $key) {
                $data[] = (float) ($grouped[$key] ?? 0);
            }
            return $data;
        };

        //----- Chart data (null = tidak ditampilkan untuk role ini)
        $chartData = [
            //----- Pendapatan per bulan dari tabel pembayaran
            'pendapatan_bulanan' => $bolehPendapatan ? [
                'labels' => $bulanLabels,
                'series' => [$runtun6Bulan(
                    DB::table('pembayaran')
                        ->selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as bulan, SUM(jumlah_bayar) as total")
                        ->where('tanggal', '>=', now()->subMonths(5)->startOfMonth())
                        ->groupBy('bulan')
                        ->pluck('total', 'bulan')
                )],
            ] : null,

            //----- Jumlah servis per status
            'status_servis' => $bolehServis ? [
                'labels' => ['Antri', 'Dikerjakan', 'Selesai', 'Diambil', 'Batal'],
                'series' => [
                    (int) (DB::table('servis')->where('status', 'antri')->count() ?: 0),
                    (int) (DB::table('servis')->where('status', 'dikerjakan')->count() ?: 0),
                    (int) (DB::table('servis')->where('status', 'selesai')->count() ?: 0),
                    (int) (DB::table('servis')->where('status', 'diambil')->count() ?: 0),
                    (int) (DB::table('servis')->where('status', 'batal')->count() ?: 0),
                ],
            ] : null,

            //----- Jumlah servis masuk per bulan
            'servis_bulanan' => $bolehServis ? [
                'labels' => $bulanLabels,
                'series' => [$runtun6Bulan(
                    DB::table('servis')
                        ->selectRaw("DATE_FORMAT(tanggal_masuk, '%Y-%m') as bulan, COUNT(*) as total")
                        ->where('tanggal_masuk', '>=', now()->subMonths(5)->startOfMonth())
                        ->groupBy('bulan')
                        ->pluck('total', 'bulan')
                )],
            ] : null,

            //----- Pendapatan per metode pembayaran
            'metode_pembayaran' => $bolehPendapatan ? [
                'labels' => ['Tunai', 'Transfer', 'QRIS', 'Debit', 'Kredit'],
                'series' => [
                    (float) (DB::table('pembayaran')->where('metode', 'tunai')->sum('jumlah_bayar') ?: 0),
                    (float) (DB::table('pembayaran')->where('metode', 'transfer')->sum('jumlah_bayar') ?: 0),
                    (float) (DB::table('pembayaran')->where('metode', 'qris')->sum('jumlah_bayar') ?: 0),
                    (float) (DB::table('pembayaran')->where('metode', 'debit')->sum('jumlah_bayar') ?: 0),
                    (float) (DB::table('pembayaran')->where('metode', 'kredit')->sum('jumlah_bayar') ?: 0),
                ],
            ] : null,

            //----- Pelanggan baru per bulan
            'pelanggan_bulanan' => $bolehPelanggan ? [
                'labels' => $bulanLabels,
                'series' => [$runtun6Bulan(
                    DB::table('pelanggan')
                        ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as bulan, COUNT(*) as total")
                        ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
                        ->groupBy('bulan')
                        ->pluck('total', 'bulan')
                )],
            ] : null,
        ];

        return view('home', [
            'pelanggan' => $countIfCanView('pelanggan', 'pelanggan'),
            'kendaraan' => $countIfCanView('kendaraan', 'kendaraan'),
            'mekanik' => $countIfCanView('mekanik', 'mekanik'),
            'servis' => $countIfCanView('servis', 'servis'),
            'kpi' => $kpi,
            'chartData' => $chartData,
        ]);
    }
}
