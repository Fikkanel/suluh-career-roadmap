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
        // 1. Jalankan seeder data master terlebih dahulu
        $this->call([
            CareerSeeder::class,
            SkillSeeder::class,
            AssessmentQuestionSeeder::class,
        ]);

        // 2. Akun Admin
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

        // 5. Akun Demo User 1 — pengguna yang sudah punya progress (untuk demo yang kaya)
        $user1 = User::factory()->create([
            'name'                 => 'Kira Musaka',
            'email'                => 'kira@demo.suluh.id',
            'is_admin'             => false,
            'role'                 => 'user',
            'province'             => 'DKI Jakarta',
            'education_level'      => 'S1',
            'work_experience'      => 'Belum ada',
            'age_range'            => '18-22',
            'exploration_readiness'=> 'tinggi',
            'is_profile_public'    => true,
            'public_username'      => 'kira-musaka',
        ]);

        // 6. Akun Demo User 2 — pengguna baru (untuk demo alur asesmen)
        User::factory()->create([
            'name'            => 'Rizky Pratama',
            'email'           => 'rizky@demo.suluh.id',
            'is_admin'        => false,
            'role'            => 'user',
            'province'        => 'Jawa Timur',
            'education_level' => 'D3',
            'work_experience' => '1-2 tahun',
            'age_range'       => '23-27',
        ]);

        // 7. Tambahkan progress awal untuk User 1 setelah karir & skill di-seed
        $this->call([
            DemoUserProgressSeeder::class,
        ]);
    }
}
