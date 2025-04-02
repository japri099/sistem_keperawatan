<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Kelas;
use App\Models\MataKuliah;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UserImport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminFakultasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Pastikan harus login
    }

    public function index(Request $request)
    {
        $query = User::query();
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('username', 'LIKE', "%$search%")
                ->orWhere('name', 'LIKE', "%$search%")
                ->orWhere('role', 'LIKE', "%$search%");
            });
        }

        if ($request->has('role') && $request->role !== '') {
            $query->where('role', $request->role);
        }

        $users = $query->where('role', '!=', 'admin_fakultas')->get();
        return view('admin_fakultas.modul_user', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users,username',
            'name' => 'required',
            'password' => 'required|min:6',
            'role' => ['required', Rule::in(['mahasiswa', 'dosen', 'instruktur', 'pimpinan_fakultas', 'mitra'])]
        ]);

        User::create([
            'username' => $request->username,
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json(['success' => 'User berhasil ditambahkan!']);
    }

    public function update(Request $request, $username)
    {
        $user = User::where('username', $username)->first();
        if (!$user) {
            return response()->json(['error' => 'User tidak ditemukan'], 404);
        }

        $user->update([
            'name' => $request->name,
            'role' => $request->role,
        ]);

        return response()->json(['success' => 'User berhasil diperbarui!']);
    }

    public function destroy($username)
    {
        $user = User::where('username', $username)->first();
        if (!$user) {
            return response()->json(['error' => 'User tidak ditemukan'], 404);
        }

        $user->delete();
        return response()->json(['success' => 'User berhasil dihapus!']);
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        try {
            Excel::import(new UserImport, $request->file('file'));
            session()->flash('success', 'Data berhasil diimport dari Excel!');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat mengimport data!');
        }

        return back();
    }

    public function kelasIndex(Request $request)
    {
        $search = $request->query('search');
        $semester = $request->query('semester');
        $kelas = Kelas::query();

        // Filter search

        if ($search) {
            $kelas->where(function($query) use ($search) {
                $query->where('nama_kelas', 'LIKE', "%$search%")
                  ->orWhere('kode_kelas', 'LIKE', "%$search%");
                });
            }

            // Filter semester
            if ($semester) {
                $kelas->where('semester', $semester);
            }

            $kelas = $kelas->get();
            $editKelas = null;
            if ($request->has('edit')) {
                $editKelas = Kelas::where('kode_kelas', $request->edit)->first();
            }
            return view('admin_fakultas.modul_kelas', compact('kelas', 'editKelas'));
        }

    public function kelasStore(Request $request) {
        $request->validate([
            'nama_kelas' => 'required|unique:kelas',
            'semester' => 'required|integer|between:1,8'
        ]);

        $tahun = now()->year;
        $baseKode = 'KLS' . substr($tahun, -3) . strtoupper(substr($request->nama_kelas, 0, 4));
        $kode = $baseKode;
        $counter = 1;

        // Cek duplikat kode_kelas
        while (Kelas::where('kode_kelas', $kode)->exists()) {
            $kode = $baseKode . $counter;
            $counter++;
        }

        Kelas::create([
            'kode_kelas' => $kode,
            'nama_kelas' => $request->nama_kelas,
            'semester' => $request->semester
        ]);

        return redirect()->route('admin_fakultas.kelas.index')->with('success', 'Kelas berhasil ditambahkan');
    }


    public function kelasUpdate(Request $request, $kode_kelas)
    {
        $kelas = Kelas::where('kode_kelas', $kode_kelas)->firstOrFail();
        $request->validate([
            'nama_kelas' => 'required|unique:kelas,nama_kelas,' . $kelas->kode_kelas . ',kode_kelas',
            'semester' => 'required|integer|between:1,8'
        ]);

        $kelas->update([
            'nama_kelas' => $request->nama_kelas,
            'semester' => $request->semester
        ]);

        return redirect()->route('admin_fakultas.kelas.index')->with('success', 'Kelas berhasil diupdate');
    }


    public function kelasDestroy($kode_kelas) {
        $kelas = Kelas::where('kode_kelas', $kode_kelas)->firstOrFail();
        $kelas->delete();

        return redirect()->route('admin_fakultas.kelas.index')->with('success', 'Kelas berhasil dihapus');
    }

    public function mataKuliahIndex(Request $request)
    {
        $search = $request->query('search');
        $mataKuliah = MataKuliah::query();
        if ($search) {
            $mataKuliah->where('nama_mk', 'LIKE', "%$search%")
            ->orWhere('kode_mk', 'LIKE', "%$search%");
        }

        $mataKuliah = $mataKuliah->get();
        $kelas = Kelas::all();
        $dosen = User::where('role', 'dosen')->get(); // Hanya dosen yang tampil

        return view('admin_fakultas.modul_mata_kuliah', compact('mataKuliah', 'kelas', 'dosen'));
    }

    // Fungsi untuk membuat singkatan kode mata kuliah
    private function generateKodeMK($nama_mk)
    {
        $words = explode(' ', strtolower($nama_mk));

        if (count($words) > 1) {
            $singkatan = '';
            foreach ($words as $word) {
                $singkatan .= substr($word, 0, 2); // Ambil 2 huruf pertama tiap kata
                }
            } else {
                $singkatan = substr($words[0], 0, 4); // Jika hanya satu kata, ambil 4 huruf pertama
                }

                // Pastikan kode unik
                $counter = 1;
                $kode_mk = strtoupper($singkatan);
                while (MataKuliah::where('kode_mk', $kode_mk)->exists()) {
                    $kode_mk = strtoupper($singkatan) . $counter;
                    $counter++;
                }

                return $kode_mk;
    }

    // Menyimpan mata kuliah baru
    public function mataKuliahStore(Request $request)
    {
        $request->validate([
            'kode_kelas' => 'required|exists:kelas,kode_kelas',
            'username' => 'required|exists:users,username',
            'nama_mk' => 'required'
        ]);

        $kode_mk = $this->generateKodeMK($request->nama_mk);

        MataKuliah::create([
            'kode_mk' => $kode_mk,
            'kode_kelas' => $request->kode_kelas,
            'username' => $request->username,
            'nama_mk' => $request->nama_mk
        ]);

        return redirect()->route('admin_fakultas.mata_kuliah.index')->with('success', 'Mata kuliah berhasil ditambahkan');
    }


    // Mengupdate mata kuliah
    public function mataKuliahUpdate(Request $request, $kode_mk)
    {
        $mataKuliah = MataKuliah::where('kode_mk', $kode_mk)->firstOrFail();

        $request->validate([
            'kode_kelas' => 'required|exists:kelas,kode_kelas',
            'username' => 'required|exists:users,username',
            'nama_mk' => 'required'
        ]);

        // Jika nama berubah, generate kode baru
        if ($request->nama_mk !== $mataKuliah->nama_mk) {
            $kode_mk_baru = $this->generateKodeMK($request->nama_mk);
        } else {
            $kode_mk_baru = $mataKuliah->kode_mk;
        }

        // Update data
        $mataKuliah->update([
            'kode_mk' => $kode_mk_baru,
            'kode_kelas' => $request->kode_kelas,
            'username' => $request->username,
            'nama_mk' => $request->nama_mk
        ]);

        return redirect()->route('admin_fakultas.mata_kuliah.index')->with('success', 'Mata kuliah berhasil diperbarui');
    }



    // Menghapus mata kuliah
    public function mataKuliahDestroy($kode_mk)
    {
        $mataKuliah = MataKuliah::where('kode_mk', $kode_mk)->firstOrFail();
        $mataKuliah->delete();

        return redirect()->route('admin_fakultas.mata_kuliah.index')->with('success', 'Mata kuliah berhasil dihapus');
    }

}
