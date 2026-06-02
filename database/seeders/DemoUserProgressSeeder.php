<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Career;
use App\Models\Skill;
use App\Models\UserProgress;
use App\Models\AssessmentResult;
use Illuminate\Database\Seeder;

class DemoUserProgressSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'kira@demo.suluh.id')->first();
        if (!$user) return;

        // Assign karir Software Engineer
        $career = Career::where('slug', 'software-engineer')->first();
        if (!$career) return;

        $user->update(['current_career_id' => $career->id]);

        // Buat hasil asesmen simulasi
        AssessmentResult::updateOrCreate(
            ['user_id' => $user->id],
            [
                'riasec_scores'   => ['R' => 55, 'I' => 82, 'A' => 60, 'S' => 45, 'E' => 38, 'C' => 70],
                'big_five_scores' => ['Openness' => 78, 'Conscientiousness' => 85, 'Extraversion' => 42, 'Agreeableness' => 60, 'Neuroticism' => 30],
                'top_career_ids'  => [$career->id],
                'crs'             => 0,
                'chosen_career_id'=> $career->id,
            ]
        );

        // Buat progress skill (sekitar 60% selesai)
        $skills = Skill::where('career_id', $career->id)->orderBy('order')->get();

        $statusMap = [
            1  => 'done',        // Variabel & Tipe Data
            2  => 'done',        // Kontrol Alur
            3  => 'done',        // Fungsi & Modularisasi
            4  => 'done',        // Version Control (Git)
            5  => 'done',        // Algoritma & Struktur Data
            6  => 'in_progress', // Pengembangan Web (HTML/CSS)
            7  => 'learning',    // Framework Backend (Laravel)
            8  => 'not_started', // Database Design & SQL
            9  => 'not_started', // API Design & REST
            10 => 'not_started', // Testing & CI/CD
        ];

        foreach ($skills as $index => $skill) {
            $order  = $index + 1;
            $status = $statusMap[$order] ?? 'not_started';

            UserProgress::updateOrCreate(
                ['user_id' => $user->id, 'skill_id' => $skill->id],
                [
                    'status'     => $status,
                    'updated_at' => match ($status) {
                        'done'        => now()->subDays(rand(5, 30)),
                        'in_progress' => now()->subDays(rand(1, 4)),
                        default       => now(),
                    },
                ]
            );
        }
    }
}
