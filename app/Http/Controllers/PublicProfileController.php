<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicProfileController extends Controller
{
    public function settings()
    {
        $user = auth()->user();
        return view('app.profile-settings', compact('user'));
    }

    public function updateSettings(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'public_username' => 'nullable|string|min:4|max:50|alpha_dash|unique:users,public_username,' . $user->id,
            'is_profile_public' => 'boolean',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'public_username.unique' => 'Username ini sudah digunakan orang lain.',
            'public_username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, strip, dan underscore.',
        ]);

        $data = [
            'name' => $request->name,
            'is_profile_public' => $request->boolean('is_profile_public'),
        ];

        // Jika user mencentang public, tapi belum punya username, otomatis generate
        if ($data['is_profile_public'] && empty($request->public_username) && empty($user->public_username)) {
            $data['public_username'] = Str::slug($request->name) . '-' . random_int(1000, 9999);
        } elseif (!empty($request->public_username)) {
            $data['public_username'] = strtolower($request->public_username);
        }

        $user->update($data);

        return back()->with('success', 'Pengaturan profil publik berhasil disimpan.');
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'avatar.image'   => 'File harus berupa gambar.',
            'avatar.mimes'   => 'Format yang didukung: JPG, PNG, WebP.',
            'avatar.max'     => 'Ukuran maksimal foto adalah 2MB.',
        ]);

        $user = auth()->user();

        // Hapus avatar lama jika ada
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return back()->with('success', 'Foto profil berhasil diperbarui!');
    }

    public function show($username)
    {
        $user = User::where('public_username', $username)
                    ->where('is_profile_public', true)
                    ->with(['currentCareer', 'progress.skill'])
                    ->firstOrFail();

        $doneProgress = $user->progress->where('status', 'done');

        // Mock Data Job Vacancies (Fitur 4.2: Integrasi Loker)
        // Di MVP ini kita menggunakan data mock berdasarkan karir pengguna
        $jobs = [];
        if ($user->currentCareer) {
            $jobs = [
                [
                    'title' => 'Junior ' . $user->currentCareer->name,
                    'company' => 'Tech Nusantara',
                    'location' => 'Jakarta (Hybrid)',
                    'match_score' => 85,
                    'type' => 'Full-time'
                ],
                [
                    'title' => $user->currentCareer->name . ' Intern',
                    'company' => 'Kreatif Studio',
                    'location' => 'Bandung',
                    'match_score' => 92,
                    'type' => 'Internship'
                ],
                [
                    'title' => 'Associate ' . $user->currentCareer->name,
                    'company' => 'Bintang Corp',
                    'location' => 'Remote',
                    'match_score' => 78,
                    'type' => 'Full-time'
                ]
            ];
        }

        return view('public.profile', compact('user', 'doneProgress', 'jobs'));
    }
}
