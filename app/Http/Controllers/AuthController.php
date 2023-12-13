<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class AuthController extends Controller
{
    public function proseslogin(Request $request)
{
    // Mencoba melakukan autentikasi pengguna dengan guard 'karyawan'
    if (Auth::guard('karyawan')->attempt(['nik' => $request->nik, 'password' => $request->password])) {
        // Autentikasi berhasil, arahkan pengguna ke rute 'dashboard'
        return redirect()->route('dashboard');
    } else {
        // Autentikasi gagal, arahkan pengguna kembali dengan pesan kesalahan
        return redirect()->back()->with(['warning' => 'NIK atau password yang Anda masukkan salah!']);
    }
}


    public function proseslogout()
    {
        if (Auth::guard('karyawan')->check()) {
            Auth::guard('karyawan')->logout();
            return redirect('/')->with(['success' => 'Anda telah berhasil keluar.']); // Pesan logout yang lebih deskriptif
        }
    }

    public function proseslogoutadmin()
    {
        if (Auth::guard('user')->check()) {
            Auth::guard('user')->logout();
            return redirect('/')->with(['success' => 'Anda telah berhasil keluar.']); // Pesan logout yang lebih deskriptif
        }
    }

    public function prosesloginadmin(Request $request)
    {
        if (Auth::guard('user')->attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect()->route('dashboardadmin'); // Mengarahkan ke rute 'dashboardadmin'
        } else {
            return redirect('/panel')->with(['warning' => 'Email atau password yang Anda masukkan salah!']); // Pesan kesalahan yang lebih deskriptif
        }
    }
}
