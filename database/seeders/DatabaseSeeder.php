<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1. Akun Admin
        User::factory()->create([
            'name'     => 'Admin Suluh',
            'email'    => 'admin@suluh.id',
            'is_admin' => true,
            'role'     => 'admin',
        ]);

        // 3. Akun Mentor
        User::factory()->create([
            'name'             => 'Budi Santoso, S.Kom',
            'email'            => 'mentor@suluh.id',
            'is_admin'         => false,
            'role'             => 'mentor',
            'province'         => 'DKI Jakarta',
            'education_level'  => 'S1',
            'work_experience'  => '3-5 tahun',
        ]);

        // 4. Akun Institusi Mitra
        User::factory()->create([
            'name'     => 'Universitas Teknologi Nusantara',
            'email'    => 'kampus@suluh.id',
            'is_admin' => false,
            'role'     => 'institution',
            'province' => 'Jawa Barat',
        ]);

    }
}
