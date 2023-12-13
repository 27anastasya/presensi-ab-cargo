<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KonfigurasiController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\DepartemenController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\Karyawan;

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

Route::middleware(['guest:karyawan'])->group(function () {
    Route::get('/', function () {
        return view('auth.login');
    })->name('login');
    Route::post('/proseslogin', [AuthController::class, 'proseslogin'])->name('proseslogin');
});

Route::middleware(['guest:user'])->group(function () {
    Route::get('/panel', function () {
        return view('auth.loginadmin');
    })->name('loginadmin');
    Route::post('/prosesloginadmin', [AuthController::class, 'prosesloginadmin'])->name('prosesloginadmin');
});

Route::middleware(['auth:karyawan'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/proseslogout', [AuthController::class, 'proseslogout'])->name('proseslogout');

    // Presensi
    Route::get('/presensi/create', [PresensiController::class, 'create']);
    Route::post('/presensi/store', [PresensiController::class, 'store']);

    // Edit Profile
    Route::get('/editprofile', [PresensiController::class, 'editprofile']);
    Route::post('/presensi/{nik}/updateprofile', [PresensiController::class, 'updateprofile']);

    // Histori
    Route::get('/presensi/histori', [PresensiController::class, 'histori']);
    Route::post('/presensi/gethistori', [PresensiController::class, 'gethistori']);

    //Izin
    Route::get('/presensi/izin', [PresensiController::class, 'izin']);
    Route::get('/presensi/buatizin', [PresensiController::class, 'buatizin']);
    Route::post('/presensi/storeizin', [PresensiController::class, 'storeizin']);
    Route::post('/presensi/cekpengajuanizin', [PresensiController::class, 'cekpengajuanizin'])->name('cekpengajuanizin');
});

Route::middleware(['auth:user'])->group(function(){
    Route::get('/proseslogoutadmin', [AuthController::class, 'proseslogoutadmin'])->name('proseslogoutadmin');
    Route::get('/panel/dashboardadmin', [DashboardController::class, 'dashboardadmin'])->name('dashboardadmin');

    //Karyawan
    Route::get('/karyawan', [KaryawanController::class, 'index'])->name('index');
    Route::post('/karyawan/store', [KaryawanController::class, 'store'])->name('store');
    Route::post('/karyawan/edit', [KaryawanController::class, 'edit'])->name('edit');
    Route::post('/karyawan/{nik}/update', [KaryawanController::class, 'update'])->name('update');
    Route::post('/karyawan/{nik}/delete', [KaryawanController::class, 'delete'])->name('delete');

    // Departemen
    Route::get('/departmen', [DepartemenController::class, 'index'])->name('index');
    Route::post('/departmen/store', [DepartemenController::class, 'store'])->name('store');
    Route::post('/departmen/edit', [DepartemenController::class, 'edit'])->name('edit');
    Route::post('/departmen/{kode_dept}/update', [DepartemenController::class, 'update'])->name('update');
    Route::post('/departmen/{kode_dept}/delete', [DepartemenController::class, 'delete'])->name('delete');

     // Presensi
     Route::get('/presensi/monitoring', [PresensiController::class, 'monitoring'])->name('monitoring');
     Route::post('/getpresensi', [PresensiController::class, 'getpresensi'])->name('getpresensi');
     Route::post('/tampilkanpeta', [PresensiController::class, 'tampilkanpeta'])->name('tampilkanpeta');
     Route::get('/presensi/laporan', [PresensiController::class, 'laporan'])->name('laporan');
     Route::post('/presensi/cetaklaporan', [PresensiController::class, 'cetaklaporan'])->name('cetaklaporan');
     Route::get('/presensi/rekap', [PresensiController::class, 'rekap'])->name('rekap');
     Route::post('/presensi/cetakrekap', [PresensiController::class, 'cetakrekap'])->name('cetakrekap');
     Route::get('/presensi/izinsakit', [PresensiController::class, 'izinsakit'])->name('izinsakit');
     Route::post('/presensi/approvedizinsakit', [PresensiController::class, 'approvedizinsakit'])->name('approvedizinsakit');
     Route::get('/presensi/{id}/batalkanizinsakit', [PresensiController::class, 'batalkanizinsakit'])->name('batalkanizinsakit');
     

     //Konfigurasi
     Route::get('/konfigurasi/lokasikantor', [KonfigurasiController::class, 'lokasikantor'])->name('lokasikantor');
     Route::post('/konfigurasi/updatelokasikantor', [KonfigurasiController::class, 'updatelokasikantor'])->name('updatelokasikantor');
});