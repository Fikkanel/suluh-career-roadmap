<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use App\Events\UserLoggedIn;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLogin() { return view('auth.login'); }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);
        if (auth()->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            UserLoggedIn::dispatch(auth()->user());
            
            $user = auth()->user();
            if ($user->is_admin) {
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($user->role === 'mentor') {
                return redirect()->intended(route('mentor.dashboard'));
            } elseif ($user->role === 'institution') {
                return redirect()->intended(route('institution.dashboard'));
            }
            
            return redirect()->intended(route('dashboard'));
        }
        return back()->withErrors(['email' => 'Email atau kata sandi tidak cocok.'])->onlyInput('email');
    }

    public function showRegister() { return view('auth.register'); }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);
        try {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => $data['password'],
            ]);
        } catch (\Illuminate\Database\UniqueConstraintViolationException | \Illuminate\Database\QueryException $e) {
            return back()->withErrors(['email' => 'Email ini sudah digunakan atau gagal mendaftar.'])->withInput();
        }
        auth()->login($user);
        return redirect()->route('onboarding');
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('landing');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Login Google gagal. Silakan coba lagi.']);
        }

        $user = User::where('google_id', $googleUser->getId())->first();

        if ($user) {
            auth()->login($user, remember: true);
            return redirect()->intended(route('dashboard'));
        }

        $existingByEmail = User::where('email', $googleUser->getEmail())->first();

        if ($existingByEmail) {
            $existingByEmail->update(['google_id' => $googleUser->getId()]);
            auth()->login($existingByEmail, remember: true);
            return redirect()->intended(route('dashboard'));
        }

        $newUser = User::create([
            'name'      => $googleUser->getName(),
            'email'     => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'password'  => Str::random(32),
        ]);
        auth()->login($newUser, remember: true);
        return redirect()->route('onboarding');
    }

    /* ── Institution Self-Register ──────────────────────────────── */

    public function showRegisterInstitution()
    {
        return view('auth.register-institution');
    }

    public function registerInstitution(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|unique:users,email',
            'password'          => 'required|min:8|confirmed',
            'institution_type'  => 'required|in:university,vocational,training_center,research',
            'institution_code'  => 'required|string|max:50',
        ]);

        // Verifikasi kode akses institusi (kode khusus yang diberikan Suluh kepada kampus mitra)
        if ($data['institution_code'] !== config('services.api.institution_code', 'SULUH-MITRA-2026')) {
            return back()->withErrors(['institution_code' => 'Kode akses institusi tidak valid. Hubungi tim Suluh untuk mendapatkan kode resmi.'])->withInput();
        }

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'],
            'role'     => 'institution',
        ]);

        auth()->login($user);
        return redirect()->route('institution.dashboard')
            ->with('success', 'Selamat datang, ' . $user->name . '! Akun Institusi Mitra Anda telah berhasil dibuat.');
    }
}
