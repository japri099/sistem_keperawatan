<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UserImport;

class AuthController extends Controller
{
    // Menampilkan halaman login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);
        
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            return redirect()->route($this->redirectRole($user->role));
        }
        
        return back()->withErrors(['username' => 'Username atau password salah']);
    }

    // Menampilkan halaman register
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Proses register manual atau import Excel
    public function register(Request $request)
    {
    // Jika ada file, redirect ke metode import
    if ($request->hasFile('file')) {
        return $this->import($request);
    }

    // Validasi untuk registrasi manual
    $request->validate([
        'username' => 'sometimes|nullable|unique:users',
        'name' => 'sometimes|nullable',
        'password' => 'required|min:6',
        'role' => 'required|in:mahasiswa,dosen,instruktur,admin_fakultas,pimpinan_fakultas,mitra'
    ]);

    // Pastikan ada username atau name
    if (!$request->username && !$request->name) {
        return back()->withErrors(['username' => 'Username atau Nama harus diisi.']);
    }

    // Simpan data user
    User::create([
        'username' => $request->username ?? null,
        'name' => $request->name ?? null,
        'password' => Hash::make($request->password),
        'role' => $request->role
    ]);

    return redirect()->route('login')->with('success', 'Registrasi berhasil, silakan login.');
}

    // Proses import user dari Excel
    public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,csv'
    ]);

    try {
        Excel::import(new UserImport, $request->file('file'));

        // Menampilkan alert sukses dan redirect ke /login
        return redirect()->route('login')->with('success', 'Data pengguna berhasil diimport. Silakan login.');
    } catch (\Exception $e) {
        return back()->withErrors(['file' => 'Terjadi kesalahan saat mengimpor file: ' . $e->getMessage()]);
    }
}


    // Menentukan redirect berdasarkan role
    private function redirectRole($role)
    {
        return match ($role) {
            'mahasiswa' => 'dashboard.mahasiswa',
            'dosen' => 'dashboard.dosen',
            'instruktur' => 'dashboard.instruktur',
            'admin_fakultas' => 'dashboard.admin_fakultas',
            'pimpinan_fakultas' => 'dashboard.pimpinan_fakultas',
            'mitra' => 'dashboard.mitra',
            default => 'home',
        };
    }

    // Logout
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
