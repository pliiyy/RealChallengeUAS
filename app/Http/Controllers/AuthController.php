<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard'); // Arahkan ke halaman setelah login
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        // 1. Menghapus informasi otentikasi (session)
        Auth::logout(); 

        // 2. Membatalkan session saat ini agar tidak bisa digunakan lagi (regenerasi session ID)
        $request->session()->invalidate(); 

        // 3. Meregenerasi token CSRF baru untuk session berikutnya
        $request->session()->regenerateToken(); 

        // 4. Mengarahkan pengguna kembali ke halaman yang diinginkan (misalnya halaman login atau home)
        return redirect('/'); 
    }
}
