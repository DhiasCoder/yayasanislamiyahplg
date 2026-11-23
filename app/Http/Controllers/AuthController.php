<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials)) {
            return back()
                ->withErrors(['email' => 'Email atau password salah.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        $roles = $user && method_exists($user, 'roles')
            ? $user->roles()->pluck('nama_role')
            : collect();

        if ($roles->contains('super_admin')) {
            return redirect()->route('superadmin.dashboard');
        }

        if ($roles->contains('admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($roles->contains('kepala_sekolah')) {
            return redirect()->route('dashboard');
        }

        if ($roles->contains('guru')) {
            return redirect()->route('guru.dashboard');
        }

        if ($roles->contains('siswa')) {
            return redirect()->route('siswa.dashboard');
        }

        if ($roles->contains('staff')) {
            return redirect()->route('dashboard');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->withErrors([
                'email' => 'Akun belum memiliki role yang valid.',
            ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
