<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect "/" ke halaman login
Route::get('/', function () {
    return redirect()->route('login');
});

// Route untuk login dan register
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard Routes berdasarkan role
Route::middleware(['auth'])->group(function () {
    Route::get('/mahasiswa/dashboard', function () {
        return view('mahasiswa.dashboard');
    })->name('dashboard.mahasiswa');

    Route::get('/dosen/dashboard', function () {
        return view('dosen.dashboard');
    })->name('dashboard.dosen');

    Route::get('/instruktur/dashboard', function () {
        return view('instruktur.dashboard');
    })->name('dashboard.instruktur');

    Route::get('/admin_fakultas/dashboard', function () {
        return view('admin_fakultas.dashboard');
    })->name('dashboard.admin_fakultas');

    Route::get('/pimpinan_fakultas/dashboard', function () {
        return view('pimpinan_fakultas.dashboard');
    })->name('dashboard.pimpinan_fakultas');

    Route::get('/mitra/dashboard', function () {
        return view('mitra.dashboard');
    })->name('dashboard.mitra');
});
