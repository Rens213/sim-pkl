<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        $kredensial = $request->validate([
            'nis_nip' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Auth::attempt membandingkan password dengan hash bcrypt di database.
        if (Auth::attempt($kredensial, $request->boolean('ingat'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['nis_nip' => 'NIS/NIP atau kata sandi salah.'])->onlyInput('nis_nip');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    /** Pendaftaran mandiri khusus siswa. Akun Hubin dan mitra dibuat lewat seeder atau menu Perusahaan. */
    public function register(Request $request)
    {
        $data = $request->validate([
            'nis_nip' => ['required', 'string', 'max:30', 'unique:users,nis_nip'],
            'nama_lengkap' => ['required', 'string', 'max:150'],
            'kelas' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create($data + ['role' => 'siswa']);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
