<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            'software-engineer' => [
                ['name' => 'Variabel & Tipe Data',         'level' => 'beginner',     'estimated_hours' => 8,  'order' => 1],
                ['name' => 'Kontrol Alur (if/loop)',       'level' => 'beginner',     'estimated_hours' => 10, 'order' => 2],
                ['name' => 'Fungsi & Modularisasi',        'level' => 'beginner',     'estimated_hours' => 12, 'order' => 3],
                ['name' => 'Version Control (Git)',        'level' => 'beginner',     'estimated_hours' => 8,  'order' => 4],
                ['name' => 'Algoritma & Struktur Data',    'level' => 'intermediate', 'estimated_hours' => 40, 'order' => 5],
                ['name' => 'Pengembangan Web (HTML/CSS)',  'level' => 'intermediate', 'estimated_hours' => 30, 'order' => 6],
                ['name' => 'Framework Backend (Laravel)',  'level' => 'intermediate', 'estimated_hours' => 60, 'order' => 7],
                ['name' => 'Database Design & SQL',        'level' => 'intermediate', 'estimated_hours' => 30, 'order' => 8],
                ['name' => 'API Design & REST',            'level' => 'advanced',     'estimated_hours' => 40, 'order' => 9],
                ['name' => 'Testing & CI/CD',              'level' => 'advanced',     'estimated_hours' => 30, 'order' => 10],
            ],
            'ux-designer' => [
                ['name' => 'Dasar-Dasar Desain Visual',       'level' => 'beginner',     'estimated_hours' => 15, 'order' => 1],
                ['name' => 'Riset Pengguna (User Research)',   'level' => 'beginner',     'estimated_hours' => 20, 'order' => 2],
                ['name' => 'Wireframing & Low-fi Prototyping', 'level' => 'beginner',     'estimated_hours' => 15, 'order' => 3],
                ['name' => 'Figma (Desain Tool)',              'level' => 'intermediate', 'estimated_hours' => 25, 'order' => 4],
                ['name' => 'High-Fidelity Prototyping',       'level' => 'intermediate', 'estimated_hours' => 25, 'order' => 5],
                ['name' => 'Usability Testing',               'level' => 'intermediate', 'estimated_hours' => 20, 'order' => 6],
                ['name' => 'Design System',                   'level' => 'advanced',     'estimated_hours' => 40, 'order' => 7],
                ['name' => 'Accessibility Design (WCAG)',      'level' => 'advanced',     'estimated_hours' => 30, 'order' => 8],
            ],
            'data-analyst' => [
                ['name' => 'SQL Dasar',               'level' => 'beginner',     'estimated_hours' => 20, 'order' => 1],
                ['name' => 'Excel & Google Sheets',   'level' => 'beginner',     'estimated_hours' => 15, 'order' => 2],
                ['name' => 'Visualisasi Data (Looker/Tableau)', 'level' => 'intermediate', 'estimated_hours' => 25, 'order' => 3],
                ['name' => 'Python untuk Analisis Data', 'level' => 'intermediate', 'estimated_hours' => 40, 'order' => 4],
                ['name' => 'Statistik Deskriptif',    'level' => 'intermediate', 'estimated_hours' => 30, 'order' => 5],
                ['name' => 'A/B Testing & Eksperimen','level' => 'advanced',     'estimated_hours' => 30, 'order' => 6],
                ['name' => 'Business Intelligence',   'level' => 'advanced',     'estimated_hours' => 40, 'order' => 7],
            ],
            'digital-marketer' => [
                ['name' => 'Dasar-Dasar Pemasaran Digital',   'level' => 'beginner',     'estimated_hours' => 15, 'order' => 1],
                ['name' => 'Strategi Konten (Content Strategy)', 'level' => 'beginner',  'estimated_hours' => 20, 'order' => 2],
                ['name' => 'SEO (Search Engine Optimization)', 'level' => 'intermediate', 'estimated_hours' => 30, 'order' => 3],
                ['name' => 'Google Ads & Meta Ads',            'level' => 'intermediate', 'estimated_hours' => 35, 'order' => 4],
                ['name' => 'Email Marketing & CRM',            'level' => 'intermediate', 'estimated_hours' => 20, 'order' => 5],
                ['name' => 'Analitik & Pelaporan (GA4)',       'level' => 'intermediate', 'estimated_hours' => 25, 'order' => 6],
                ['name' => 'Growth Hacking',                   'level' => 'advanced',     'estimated_hours' => 40, 'order' => 7],
            ],
            'product-manager' => [
                ['name' => 'Product Thinking & Mindset',       'level' => 'beginner',     'estimated_hours' => 20, 'order' => 1],
                ['name' => 'User Story & Requirement Writing',  'level' => 'beginner',     'estimated_hours' => 15, 'order' => 2],
                ['name' => 'Agile & Scrum Framework',          'level' => 'intermediate', 'estimated_hours' => 25, 'order' => 3],
                ['name' => 'Analisis Data Produk',             'level' => 'intermediate', 'estimated_hours' => 30, 'order' => 4],
                ['name' => 'Roadmap Prioritization (RICE)',    'level' => 'intermediate', 'estimated_hours' => 20, 'order' => 5],
                ['name' => 'Stakeholder Management',           'level' => 'advanced',     'estimated_hours' => 35, 'order' => 6],
                ['name' => 'Go-to-Market Strategy',            'level' => 'advanced',     'estimated_hours' => 40, 'order' => 7],
            ],
            'cybersecurity-analyst' => [
                ['name' => 'Jaringan Komputer Dasar (TCP/IP)', 'level' => 'beginner',     'estimated_hours' => 25, 'order' => 1],
                ['name' => 'Linux & Command Line',             'level' => 'beginner',     'estimated_hours' => 20, 'order' => 2],
                ['name' => 'Kriptografi Dasar',                'level' => 'beginner',     'estimated_hours' => 15, 'order' => 3],
                ['name' => 'Ethical Hacking & Penetration Testing', 'level' => 'intermediate', 'estimated_hours' => 50, 'order' => 4],
                ['name' => 'SIEM & Log Analysis',              'level' => 'intermediate', 'estimated_hours' => 30, 'order' => 5],
                ['name' => 'Incident Response',                'level' => 'advanced',     'estimated_hours' => 40, 'order' => 6],
                ['name' => 'Keamanan Aplikasi Web (OWASP)',    'level' => 'advanced',     'estimated_hours' => 45, 'order' => 7],
            ],
            'business-analyst' => [
                ['name' => 'Dasar-Dasar Analisis Bisnis',          'level' => 'beginner',     'estimated_hours' => 20, 'order' => 1],
                ['name' => 'Pemodelan Proses Bisnis (BPMN)',        'level' => 'beginner',     'estimated_hours' => 20, 'order' => 2],
                ['name' => 'Elicitation & Dokumentasi Kebutuhan',  'level' => 'intermediate', 'estimated_hours' => 30, 'order' => 3],
                ['name' => 'SQL untuk Analisis Bisnis',            'level' => 'intermediate', 'estimated_hours' => 25, 'order' => 4],
                ['name' => 'Analisis Data & Pelaporan',            'level' => 'intermediate', 'estimated_hours' => 30, 'order' => 5],
                ['name' => 'Manajemen Perubahan (Change Mgmt)',     'level' => 'advanced',     'estimated_hours' => 35, 'order' => 6],
            ],
            'cloud-engineer' => [
                ['name' => 'Dasar-Dasar Cloud Computing',       'level' => 'beginner',     'estimated_hours' => 20, 'order' => 1],
                ['name' => 'Linux & Shell Scripting',           'level' => 'beginner',     'estimated_hours' => 25, 'order' => 2],
                ['name' => 'AWS / GCP / Azure Core Services',   'level' => 'intermediate', 'estimated_hours' => 50, 'order' => 3],
                ['name' => 'Infrastruktur sebagai Kode (Terraform)', 'level' => 'intermediate', 'estimated_hours' => 40, 'order' => 4],
                ['name' => 'Docker & Kubernetes',               'level' => 'intermediate', 'estimated_hours' => 45, 'order' => 5],
                ['name' => 'CI/CD Pipeline',                   'level' => 'advanced',     'estimated_hours' => 35, 'order' => 6],
                ['name' => 'Keamanan & Kepatuhan Cloud',        'level' => 'advanced',     'estimated_hours' => 40, 'order' => 7],
            ],
            'guru-sd' => [
                ['name' => 'Manajemen Kelas & Kondusivitas',       'level' => 'beginner',     'estimated_hours' => 20, 'order' => 1],
                ['name' => 'Pengembangan Kurikulum & RPP',         'level' => 'beginner',     'estimated_hours' => 25, 'order' => 2],
                ['name' => 'Psikologi Perkembangan Anak SD',       'level' => 'intermediate', 'estimated_hours' => 30, 'order' => 3],
                ['name' => 'Penilaian & Evaluasi Pembelajaran',    'level' => 'intermediate', 'estimated_hours' => 25, 'order' => 4],
                ['name' => 'Media & Alat Peraga Pembelajaran Kreatif', 'level' => 'intermediate', 'estimated_hours' => 30, 'order' => 5],
                ['name' => 'Konseling Siswa & Hubungan Wali Murid', 'level' => 'advanced',     'estimated_hours' => 35, 'order' => 6],
            ],
            'guru-bimbel' => [
                ['name' => 'Teknik Mengajar Interaktif & Seru',    'level' => 'beginner',     'estimated_hours' => 15, 'order' => 1],
                ['name' => 'Pemahaman Kurikulum & Bank Soal',       'level' => 'beginner',     'estimated_hours' => 20, 'order' => 2],
                ['name' => 'Diagnosis Kesulitan Belajar Siswa',     'level' => 'intermediate', 'estimated_hours' => 25, 'order' => 3],
                ['name' => 'Penyusunan Modul & Ringkasan Cepat',   'level' => 'intermediate', 'estimated_hours' => 30, 'order' => 4],
                ['name' => 'Komunikasi Persuasif & Motivasi Belajar', 'level' => 'advanced',   'estimated_hours' => 35, 'order' => 5],
            ],
            'penulis-parenting' => [
                ['name' => 'Keterampilan Menulis Kreatif & Edukatif', 'level' => 'beginner',   'estimated_hours' => 20, 'order' => 1],
                ['name' => 'Riset & Teori Psikologi Ibu-Anak',      'level' => 'beginner',     'estimated_hours' => 25, 'order' => 2],
                ['name' => 'Copywriting & Content Creation Media',  'level' => 'intermediate', 'estimated_hours' => 30, 'order' => 3],
                ['name' => 'Strategi Publikasi Buku & Kerja Penerbit', 'level' => 'advanced',   'estimated_hours' => 35, 'order' => 4],
            ],
            'pns-adm' => [
                ['name' => 'Tata Kelola Administrasi & Kearsipan',  'level' => 'beginner',     'estimated_hours' => 15, 'order' => 1],
                ['name' => 'Penyusunan Surat Dinas & Notulensi',    'level' => 'beginner',     'estimated_hours' => 20, 'order' => 2],
                ['name' => 'Regulasi Pemerintah & Kebijakan Publik', 'level' => 'intermediate', 'estimated_hours' => 30, 'order' => 3],
                ['name' => 'Pelayanan Prima & Public Speaking',     'level' => 'intermediate', 'estimated_hours' => 25, 'order' => 4],
                ['name' => 'Manajemen Keuangan Instansi Dasar',      'level' => 'advanced',     'estimated_hours' => 35, 'order' => 5],
            ],
        ];

        foreach ($skills as $slug => $careerSkills) {
            $career = \App\Models\Career::where('slug', $slug)->first();
            if (!$career) continue;
            foreach ($careerSkills as $skill) {
                \App\Models\Skill::updateOrCreate(
                    ['career_id' => $career->id, 'name' => $skill['name']],
                    array_merge($skill, ['career_id' => $career->id])
                );
            }
        }
    }
}
