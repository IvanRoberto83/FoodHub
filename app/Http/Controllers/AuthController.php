<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    private array $messages = [
        'required'  => ':attribute wajib diisi.',
        'email'     => 'Format email tidak valid.',
        'unique'    => ':attribute sudah terdaftar.',
        'min'       => ':attribute minimal :min karakter.',
        'max'       => ':attribute maksimal :max karakter.',
        'confirmed' => 'Konfirmasi :attribute tidak cocok.',
        'in'        => ':attribute tidak valid.',
    ];

    private array $attributes = [
        'name'     => 'Nama',
        'email'    => 'Email',
        'phone'    => 'No. telepon',
        'role'     => 'Jenis akun',
        'password' => 'Password',
    ];

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], $this->messages, $this->attributes);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return $this->redirectByRole(Auth::user());
        }

        return back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'min:8', 'confirmed'],
        ], $this->messages, $this->attributes);

        // Akun pendaftar publik otomatis diset sebagai customer
        $data['role'] = 'customer';

        $user = User::create($data);

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectByRole($user);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function redirectByRole(User $user)
    {
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'owner' => redirect()->route('owner.dashboard'),
            default => redirect()->route('home'),
        };
    }
}