<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminFakultasController;


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
    // mahasiswa
    Route::get('/mahasiswa/dashboard', function () {
        return view('mahasiswa.dashboard');
    })->name('dashboard.mahasiswa');

    // dosen
    Route::get('/dosen/dashboard', function () {
        return view('dosen.dashboard');
    })->name('dashboard.dosen');


    // instruktur
    Route::get('/instruktur/dashboard', function () {
        return view('instruktur.dashboard');
    })->name('dashboard.instruktur');


    // admin
    Route::get('/admin_fakultas/dashboard', function () {
        return view('admin_fakultas.dashboard');
    })->name('dashboard.admin_fakultas');
    Route::get('/admin_fakultas/user', [AdminFakultasController::class, 'index'])->name('admin_fakultas.user.index');
    Route::post('/admin_fakultas/user', [AdminFakultasController::class, 'store'])->name('admin_fakultas.user.store');
    Route::put('/admin_fakultas/user/{username}', [AdminFakultasController::class, 'update'])->name('admin_fakultas.user.update');
    Route::delete('/admin_fakultas/user/{username}', [AdminFakultasController::class, 'destroy'])->name('admin_fakultas.user.destroy');
    Route::post('/admin_fakultas/user/import', [AdminFakultasController::class, 'importExcel'])->name('admin_fakultas.user.import');


    // pimpinan
    Route::get('/pimpinan_fakultas/dashboard', function () {
        return view('pimpinan_fakultas.dashboard');
    })->name('dashboard.pimpinan_fakultas');


    // mitra
    Route::get('/mitra/dashboard', function () {
        return view('mitra.dashboard');
    })->name('dashboard.mitra');
});
