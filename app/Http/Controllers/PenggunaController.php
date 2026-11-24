<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Exports\PenggunaExport;
use App\Support\RolePermission;
use Maatwebsite\Excel\Facades\Excel;

//----- PenggunaController: menangani CRUD data pengguna/pengguna sistem beserta export PDF/Excel
class PenggunaController extends Controller
{
    // ==================== PENGGUNA ====================

    //----- pengguna(): Menampilkan halaman daftar pengguna/pengguna sistem
    public function pengguna()
    {
        RolePermission::cek('view:pengguna');

        return view('pengguna', ['rows' => $this->penggunaData()]);
    }

    //----- penggunaData(): Mengambil data users untuk ditampilkan (private)
    private function penggunaData()
    {
        return DB::table('users')
            ->select('id', 'nama', 'email', 'role', 'no_hp', 'is_active', 'foto', 'created_at')
            ->get();
    }

    //----- tambahPengguna(): Menampilkan form tambah pengguna baru
    public function tambahPengguna()
    {
        RolePermission::cek('write:pengguna');

        return view('input_pengguna');
    }

    //----- simpanPengguna(): Menyimpan pengguna baru
    public function simpanPengguna(Request $request)
    {
        RolePermission::cek('write:pengguna');

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => session('role') === 'owner'
                ? 'required|in:' . implode(',', RolePermission::ROLES)
                : 'required|in:admin,kasir,mekanik',
            'no_hp' => 'nullable|string|max:20',
            'foto' => 'nullable|image|max:2048',
        ]);

        try {
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads'), $filename);
                $fotoPath = $filename;
            }

            $tersimpan = DB::table('users')->insert([
                'nama' => $validated['nama'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'no_hp' => $validated['no_hp'],
                'foto' => $fotoPath,
                'is_active' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (!$tersimpan) {
                return redirect('/pengguna')->with('gagal', 'Data pengguna gagal disimpan ke database. Silakan coba lagi.');
            }

            return redirect('/pengguna')->with('sukses', 'Data pengguna berhasil ditambahkan.');
        } catch (\Throwable $e) {
            return redirect('/pengguna')->with('gagal', 'Data pengguna gagal disimpan ke database. Silakan coba lagi.');
        }
    }

    //----- editPengguna(): Menampilkan form edit pengguna berdasarkan id
    public function editPengguna($id)
    {
        RolePermission::cek('write:pengguna');

        $user = DB::table('users')->where('id', $id)->first();
        abort_if($user === null, 404);

        return view('edit_pengguna', ['row' => $user]);
    }

    //----- updatePengguna(): Memperbarui data pengguna dan sinkronkan session jika mengedit diri sendiri
    public function updatePengguna(Request $request, $id)
    {
        RolePermission::cek('write:pengguna');

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'role' => session('role') === 'owner'
                ? 'required|in:' . implode(',', RolePermission::ROLES)
                : 'required|in:admin,kasir,mekanik',
            'no_hp' => 'nullable|string|max:20',
            'foto' => 'nullable|image|max:2048',
        ]);

        $user = DB::table('users')->where('id', $id)->first();
        abort_if($user === null, 404);

        $data = [
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'no_hp' => $validated['no_hp'],
            'updated_at' => now(),
        ];

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);
            $data['foto'] = $filename;

            if ($user->foto && file_exists(public_path('uploads/' . $user->foto))) {
                unlink(public_path('uploads/' . $user->foto));
            }
        }

        if ($validated['password'] ?? null) {
            $data['password'] = Hash::make($validated['password']);
        }

        DB::table('users')->where('id', $id)->update($data);

        if ((int) session('id_user') === (int) $id) {
            $userUpdate = DB::table('users')->where('id', $id)->first();
            session([
                'u' => $validated['nama'],
                'role' => $validated['role'],
                'foto' => $userUpdate->foto,
            ]);
        }

        return redirect('/pengguna');
    }

    //----- hapusPengguna(): Menghapus data pengguna berdasarkan id
    public function hapusPengguna($id)
    {
        RolePermission::cek('write:pengguna');

        //----- Jangan sampai user menghapus akunnya sendiri
        abort_if((int) $id === (int) session('id_user'), 403);

        DB::table('users')->where('id', $id)->delete();
        return redirect('/pengguna');
    }

    //----- pdfPengguna(): Mengunduh laporan data pengguna dalam format PDF
    public function pdfPengguna()
    {
        RolePermission::cek('export:pengguna');

        $export = new PenggunaExport($this->penggunaData());

        $this->pdfTable('Laporan Data Pengguna', 'laporan-pengguna', $export->headings(), $export->collection()->all());
    }

    //----- excelPengguna(): Mengunduh laporan data pengguna dalam format Excel (.xlsx)
    public function excelPengguna()
    {
        RolePermission::cek('export:pengguna');

        return Excel::download(
            new PenggunaExport($this->penggunaData()),
            'laporan-pengguna-' . date('Y-m-d') . '.xlsx'
        );
    }
}
