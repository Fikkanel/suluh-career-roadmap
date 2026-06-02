<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CareerSeeder extends Seeder
{
    public function run(): void
    {
        $careers = [
            [
                'name'              => 'Software Engineer',
                'slug'              => 'software-engineer',
                'description'       => 'Merancang, membangun, dan memelihara sistem perangkat lunak. Karir ini sangat beragam — dari backend, frontend, hingga mobile dan embedded systems. Sangat dicari oleh startup, perusahaan teknologi, dan sektor perbankan digital di Indonesia.',
                'riasec_code'       => 'IRA',
                'industry_standard' => 'Teknologi Informasi',
            ],
            [
                'name'              => 'UX/UI Designer',
                'slug'              => 'ux-designer',
                'description'       => 'Merancang pengalaman pengguna yang intuitif dan antarmuka yang estetis. Menjembatani kebutuhan manusia dan kemampuan teknologi melalui riset dan desain iteratif. Profil ini dibutuhkan di hampir setiap perusahaan digital.',
                'riasec_code'       => 'ASI',
                'industry_standard' => 'Desain Digital',
            ],
            [
                'name'              => 'Data Analyst',
                'slug'              => 'data-analyst',
                'description'       => 'Mengolah dan menginterpretasikan data untuk mendukung keputusan bisnis. Profesi ini semakin krusial seiring dengan banjirnya data di era digital — dari e-commerce hingga layanan kesehatan.',
                'riasec_code'       => 'ICR',
                'industry_standard' => 'Data & Analitik',
            ],
            [
                'name'              => 'Digital Marketing Specialist',
                'slug'              => 'digital-marketer',
                'description'       => 'Merencanakan dan menjalankan strategi pemasaran di saluran digital (SEO, SEM, Media Sosial, Email Marketing, Content). Sangat relevan untuk UMKM hingga korporasi besar yang ingin memperluas jangkauan online mereka di Indonesia.',
                'riasec_code'       => 'EAS',
                'industry_standard' => 'Pemasaran Digital',
            ],
            [
                'name'              => 'Product Manager',
                'slug'              => 'product-manager',
                'description'       => 'Mengelola siklus hidup sebuah produk digital dari ide hingga peluncuran. Menjadi jembatan antara tim bisnis, desain, dan engineering untuk memastikan produk yang dibangun benar-benar memecahkan masalah pengguna.',
                'riasec_code'       => 'EIR',
                'industry_standard' => 'Manajemen Produk',
            ],
            [
                'name'              => 'Cybersecurity Analyst',
                'slug'              => 'cybersecurity-analyst',
                'description'       => 'Melindungi sistem, jaringan, dan data dari ancaman siber. Dengan meningkatnya kasus kebocoran data dan serangan ransomware di Indonesia, profesi ini semakin kritis dan berpeluang gaji tinggi di sektor keuangan dan pemerintahan.',
                'riasec_code'       => 'IRC',
                'industry_standard' => 'Keamanan Siber',
            ],
            [
                'name'              => 'Business Analyst',
                'slug'              => 'business-analyst',
                'description'       => 'Menganalisis proses bisnis dan menerjemahkannya menjadi kebutuhan teknis yang dapat dikerjakan oleh tim pengembang. Menjadi penghubung antara dunia bisnis dan teknologi di berbagai industri.',
                'riasec_code'       => 'IEC',
                'industry_standard' => 'Bisnis & Teknologi',
            ],
            [
                'name'              => 'Cloud Engineer',
                'slug'              => 'cloud-engineer',
                'description'       => 'Membangun, mengelola, dan mengoptimalkan infrastruktur berbasis cloud (AWS, GCP, Azure). Seiring dengan migrasi masif perusahaan Indonesia ke cloud, profesi ini mengalami kekurangan tenaga yang signifikan.',
                'riasec_code'       => 'IRA',
                'industry_standard' => 'Infrastruktur Cloud',
            ],
        ];

        foreach ($careers as $career) {
            \App\Models\Career::updateOrCreate(['slug' => $career['slug']], $career);
        }
    }
}
