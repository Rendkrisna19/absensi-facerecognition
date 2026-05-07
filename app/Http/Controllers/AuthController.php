<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User; // Pastikan model User di-import

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectUser(Auth::user()->role);
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validasi input wajib diisi
        $request->validate([
            'nik' => ['required', 'string'],
            'password' => ['required'],
        ]);

        // 1. Cari user berdasarkan NIK
        $user = User::where('nik', $request->nik)->first();

        if (!$user) {
            // NIK tidak ditemukan dalam database
            return back()->withErrors([
                'nik' => 'NIK tidak terdaftar di sistem.',
            ])->onlyInput('nik');
        }

        // 2. Jika NIK ada, cek kecocokan password
        if (!Hash::check($request->password, $user->password)) {
            // Password salah
            return back()->withErrors([
                'password' => 'Password yang Anda masukkan salah.',
            ])->onlyInput('nik'); // Tetap simpan input NIK agar user tidak perlu mengetik ulang
        }

        // 3. Jika NIK dan Password benar, proses login
        Auth::login($user);
        $request->session()->regenerate();
        
        return $this->redirectUser($user->role);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    private function redirectUser($role)
    {
        switch ($role) {
            case 'admin':
                return redirect()->intended('/admin/dashboard');
            case 'kepala_yayasan':
                return redirect()->intended('/yayasan/dashboard');
            case 'guru':
                return redirect()->intended('/guru/dashboard');
            default:
                return redirect('/');
        }
    }
}