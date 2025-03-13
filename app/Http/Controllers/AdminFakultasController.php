<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UserImport;
use Illuminate\Support\Facades\Auth;

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

}
