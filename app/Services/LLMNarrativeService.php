<?php

namespace App\Services;

use App\Models\LlmNarrativeCache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LLMNarrativeService
{
    /**
     * Fallback chain and cooperative combination: Groq -> Gemini -> Rule-based template.
     * If both keys are present, Groq generates the raw draft and Gemini refines it.
     */
    public function generate(int $userId, string $type, array $context): string
    {
        $profileHash = md5(json_encode($context));

        $cached = LlmNarrativeCache::where('user_id', $userId)
            ->where('type', $type)
            ->where('profile_hash', $profileHash)
            ->first();

        if ($cached) {
            return $cached->narrative;
        }

        // 1. Try to get a raw draft using Groq first
        $result = $this->tryGroq($type, $context);
        $provider = 'groq';

        if (!$result) {
            // Fallback: Try Gemini directly
            $result = $this->tryGemini($type, $context);
            $provider = 'gemini';
        }

        if ($result) {
            $narrative = $result[0];
            $provider = $result[1];

            // 2. Cooperative refinement: If both keys are active, let Gemini refine the Groq draft!
            $geminiKey = config('services.gemini.api_key');
            $groqKeys = config('services.groq.api_keys');
            $groqKeyExists = !empty($groqKeys) || config('services.groq.api_key');

            if ($geminiKey && $groqKeyExists && $provider === 'groq' && $type !== 'custom_questions' && $type !== 'job_recommendations' && $type !== 'career_recommendation' && $type !== 'roadmap_generation') {
                $refined = $this->refineWithGemini($narrative, $type, $context);
                if ($refined) {
                    $narrative = $refined;
                    $provider = 'groq_gemini_combined';
                }
            }
        } else {
            // 3. Rule-based fallback if all AI APIs fail
            $narrative = $this->ruleBasedFallback($type, $context);
            $provider = 'rule_based';
        }

        LlmNarrativeCache::create([
            'user_id'      => $userId,
            'type'         => $type,
            'profile_hash' => $profileHash,
            'narrative'    => $narrative,
            'provider'     => $provider,
            'generated_at' => now(),
        ]);

        return $narrative;
    }

    /* ── Gemini Flash ─────────────────────────────────────── */

    private function tryGemini(string $type, array $context): ?array
    {
        $apiKeys = config('services.gemini.api_keys');
        if (empty($apiKeys)) {
            $singleKey = config('services.gemini.api_key');
            $apiKeys = $singleKey ? [$singleKey] : [];
        }

        if (empty($apiKeys)) {
            return null;
        }

        try {
            $prompt = $this->buildPrompt($type, $context);
            if ($type !== 'job_recommendations' && $type !== 'custom_questions' && $type !== 'career_recommendation' && $type !== 'roadmap_generation') {
                $prompt = "You are a professional career guidance assistant. Speak in Bahasa Indonesia. Output ONLY the response text directly, without any conversational preamble, meta-talk, or conversational fillers (like 'Tentu', 'Berikut adalah', etc.).\n\nTask: " . $prompt;
            }

            $maxTokens = ($type === 'custom_questions' || $type === 'career_recommendation' || $type === 'roadmap_generation') ? 4000 : 400;
            $timeout = ($type === 'custom_questions' || $type === 'career_recommendation' || $type === 'roadmap_generation') ? 30 : 10;

            foreach ($apiKeys as $index => $apiKey) {
                $apiKey = trim($apiKey);
                if (empty($apiKey)) {
                    continue;
                }

                try {
                    $response = Http::timeout($timeout)->post(
                        config('services.gemini.endpoint') . '?key=' . $apiKey,
                        [
                            'contents' => [
                                ['parts' => [['text' => $prompt]]]
                            ],
                            'generationConfig' => [
                                'maxOutputTokens' => $maxTokens,
                                'temperature'     => 0.7,
                            ],
                        ]
                    );

                    if ($response->successful()) {
                        $text = $response->json('candidates.0.content.parts.0.text');
                        if ($text) {
                            $cleaned = $this->cleanJsonString($text);
                            return [$cleaned, 'gemini'];
                        }
                    }

                    Log::warning("Gemini API key at index $index failed with status " . $response->status() . ": " . $response->body());
                } catch (\Throwable $e) {
                    Log::warning("Gemini API key at index $index failed with exception: " . $e->getMessage());
                }
            }
        } catch (\Throwable $e) {
            Log::warning('LLM Gemini try wrapper failed: ' . $e->getMessage());
        }

        return null;
    }

    /* ── Groq (llama3) ────────────────────────────────────── */

    private function tryGroq(string $type, array $context): ?array
    {
        $apiKeys = config('services.groq.api_keys');
        if (empty($apiKeys)) {
            $singleKey = config('services.groq.api_key');
            $apiKeys = $singleKey ? [$singleKey] : [];
        }

        if (empty($apiKeys)) {
            return null;
        }

        $maxTokens = ($type === 'custom_questions' || $type === 'career_recommendation' || $type === 'roadmap_generation') ? 4000 : 400;
        $timeout = ($type === 'custom_questions' || $type === 'career_recommendation' || $type === 'roadmap_generation') ? 30 : 10;
        $systemPrompt = ($type === 'custom_questions' || $type === 'career_recommendation' || $type === 'roadmap_generation')
            ? 'Kamu adalah pakar kurikulum karir dinamis. Tuliskan output murni JSON valid tanpa penambahan kata atau penjelasan apa pun.'
            : 'Kamu adalah asisten karir yang empatik, ringkas, dan tidak menghakimi. Tulis dalam Bahasa Indonesia. Untuk narasi umum, maksimal 3 kalimat.';

        foreach ($apiKeys as $index => $apiKey) {
            $apiKey = trim($apiKey);
            if (empty($apiKey)) {
                continue;
            }

            try {
                $response = Http::timeout($timeout)
                    ->withToken($apiKey)
                    ->post(config('services.groq.endpoint'), [
                        'model'       => config('services.groq.model', 'meta-llama/llama-4-scout-17b-16e-instruct'),
                        'messages'    => [
                            ['role' => 'system', 'content' => $systemPrompt],
                            ['role' => 'user',   'content' => $this->buildPrompt($type, $context)],
                        ],
                        'max_tokens'  => $maxTokens,
                        'temperature' => 0.7,
                    ]);

                if ($response->successful()) {
                    $text = $response->json('choices.0.message.content');
                    if ($text) {
                        $cleaned = $this->cleanJsonString($text);
                        return [$cleaned, 'groq'];
                    }
                }

                Log::warning("Groq API key at index $index failed with status " . $response->status() . ": " . $response->body());
            } catch (\Throwable $e) {
                Log::warning("Groq API key at index $index failed with exception: " . $e->getMessage());
            }
        }

        return null;
    }

    /* ── Gemini Cooperative Refinement ─────────────────────── */

    private function refineWithGemini(string $draft, string $type, array $context): ?string
    {
        $apiKeys = config('services.gemini.api_keys');
        if (empty($apiKeys)) {
            $singleKey = config('services.gemini.api_key');
            $apiKeys = $singleKey ? [$singleKey] : [];
        }

        if (empty($apiKeys)) {
            return null;
        }

        try {
            $prompt = sprintf(
                "You are an automated career guidance text polisher. Refine and polish the following career guidance draft. Make it warmer, more empathetic, encouraging, and natural in Bahasa Indonesia. Keep it to a maximum of 3 sentences. You must output ONLY the refined text itself. Do NOT include any introductory or conversational text (such as 'Tentu...', 'Berikut...', 'Ini adalah...', etc.).\n\nDraft:\n%s",
                $draft
            );

            if ($type === 'job_recommendations') {
                $prompt = sprintf(
                    "You are a career helper. You are given a JSON array of job recommendations. Check if it is a valid JSON. If it is valid, verify the job details are realistic, refine any wording inside the JSON values to be extremely professional and encouraging in Bahasa Indonesia, and return the modified JSON. If it is not valid, correct it to be valid. You must output ONLY valid JSON without any markdown formatting, code block markers (like ```json), or extra text.\n\nJSON:\n%s",
                    $draft
                );
            }

            foreach ($apiKeys as $index => $apiKey) {
                $apiKey = trim($apiKey);
                if (empty($apiKey)) {
                    continue;
                }

                try {
                    $response = Http::timeout(10)->post(
                        config('services.gemini.endpoint') . '?key=' . $apiKey,
                        [
                            'contents' => [
                                ['parts' => [['text' => $prompt]]]
                            ],
                            'generationConfig' => [
                                'maxOutputTokens' => 500,
                                'temperature'     => 0.5,
                            ],
                        ]
                    );

                    if ($response->successful()) {
                        $text = $response->json('candidates.0.content.parts.0.text');
                        if ($text) {
                            $cleaned = $this->cleanJsonString($text);
                            return $cleaned;
                        }
                    }

                    Log::warning("Gemini refinement API key at index $index failed with status " . $response->status() . ": " . $response->body());
                } catch (\Throwable $e) {
                    Log::warning("Gemini refinement API key at index $index failed with exception: " . $e->getMessage());
                }
            }
        } catch (\Throwable $e) {
            Log::warning('LLM Gemini refinement wrapper failed: ' . $e->getMessage());
        }

        return null;
    }

    /* ── Prompt Builder ───────────────────────────────────── */

    private function buildPrompt(string $type, array $context): string
    {
        return match ($type) {
            'custom_questions' => sprintf(
                "Anda adalah psikolog karir dan pakar instruksional profesional. Tugas Anda adalah menyesuaikan/menulis ulang skenario kuesioner asesmen karir agar sangat kontekstual dengan latar belakang jurusan kuliah pengguna: \"%s\".\n\n".
                "Berikut adalah daftar soal asli dalam format JSON:\n%s\n\n".
                "ATURAN PENTING:\n".
                "1. Ubah 'prompt' (skenario) pada setiap soal agar menggunakan situasi, istilah, lingkungan, atau tantangan sehari-hari yang sangat akrab bagi mahasiswa atau lulusan jurusan \"%s\" (misalnya jika Pendidikan Guru Sekolah Dasar / PGSD, gunakan skenario seputar sekolah dasar, kelas, membimbing siswa, menyusun rencana ajar, rapat guru, dll.).\n".
                "2. Untuk soal bertipe 'single_choice', Anda juga HARUS menulis ulang label pilihan jawaban ('options' a, b, c, d) agar sesuai dengan skenario kontekstual baru tersebut.\n".
                "3. Anda HARUS mempertahankan parameter penilaian secara presisi! Arti semantik dari setiap pilihan jawaban a, b, c, d (a=sangat proaktif/pemimpin, b=kolaboratif/partisipatif, c=menghindari/pasif, d=di balik layar) HARUS tetap sama persis.\n".
                "4. Anda HARUS mengembalikan struktur JSON berupa array berisi 30 objek dengan key berikut: 'id', 'prompt', 'options' (options hanya diisi jika bertipe single_choice, selain itu bernilai null). Jangan sertakan key lain!\n".
                "5. Kembalikan HANYA string JSON valid murni. JANGAN sertakan penjelasan apa pun, jangan sertakan backticks ```json atau penutup apa pun.",
                $context['major'] ?? 'Umum',
                json_encode($context['questions'], JSON_UNESCAPED_UNICODE),
                $context['major'] ?? 'Umum'
            ),
            'roadmap_generation' => sprintf(
                "Anda adalah pakar instruksional dan penasihat karir profesional. Tugas Anda adalah menghasilkan kurikulum/roadmap belajar terstruktur berupa TEPAT 6-8 skill untuk karir \"%s\".\n".
                "Sesuaikan juga skill yang dihasilkan agar relevan dengan latar belakang jurusan kuliah pengguna: \"%s\".\n".
                "Gunakan Bahasa Indonesia yang profesional dan mudah dipahami.\n\n".
                "Ketentuan/Kriteria Skill:\n".
                "1. Total skill yang dihasilkan harus berkisar antara 6 hingga 8 skill.\n".
                "2. Bagikan secara merata ke dalam 3 tingkat kesulitan/level: 'beginner', 'intermediate', dan 'advanced'.\n".
                "3. Setiap skill harus memiliki estimasi jam belajar ('estimated_hours') yang masuk akal antara 5 hingga 60 jam.\n".
                "4. Urutan belajar ('order') harus berurutan secara logis dari 1 hingga N.\n".
                "5. Sertakan juga 'validation_type' untuk setiap skill. Nilainya harus salah satu dari: 'none', 'scenario', 'reflection', 'behavior'.\n".
                "6. Jika 'validation_type' adalah 'scenario', maka field 'scenario_question' HARUS diisi dengan pertanyaan kasus nyata/skenario yang menantang pemikiran kritis tentang skill tersebut (dalam Bahasa Indonesia). Selain itu, set 'scenario_question' ke null atau hilangkan.\n\n".
                "Format output harus berupa JSON array valid seperti berikut (jangan sertakan markdown, backticks ```json, atau penjelasan lain di luar JSON):\n".
                "[\n".
                "  {\n".
                "    \"name\": \"Nama skill 1 (misal: Fondasi Pemrograman Web)\",\n".
                "    \"level\": \"beginner\",\n".
                "    \"estimated_hours\": 20,\n".
                "    \"order\": 1,\n".
                "    \"validation_type\": \"scenario\",\n".
                "    \"scenario_question\": \"Skenario kasus untuk menguji pemahaman Fondasi Pemrograman Web...\"\n".
                "  },\n".
                "  {\n".
                "    \"name\": \"Nama skill 2 (misal: Manajemen State)\",\n".
                "    \"level\": \"intermediate\",\n".
                "    \"estimated_hours\": 30,\n".
                "    \"order\": 2,\n".
                "    \"validation_type\": \"reflection\",\n".
                "    \"scenario_question\": null\n".
                "  }\n".
                "]\n\n".
                "Anda HARUS mengembalikan hanya output JSON murni tanpa penjelasan tambahan, tanpa markdown, dan tanpa backticks ```json.",
                $context['career_name'] ?? 'Karir Pilihan',
                $context['major'] ?? 'Umum'
            ),
            'chatbot_response' => sprintf(
                "Anda adalah Suluh Career Bot, asisten pembimbing karir AI yang ramah, profesional, dan empatik. Tugas Anda adalah membantu menjawab pertanyaan pengguna tentang karir, pemilihan jurusan, rencana belajar, atau persiapan kerja.\n".
                "Profil Pengguna saat ini:\n".
                "- Jurusan: %s\n".
                "- Karir Pilihan: %s\n\n".
                "Berikut adalah riwayat percakapan sebelumnya:\n%s\n".
                "Pertanyaan/Pesan Pengguna terbaru: \"%s\"\n\n".
                "Berikan jawaban Anda secara ringkas (maksimal 2-3 paragraf pendek), hangat, suportif, menggunakan Bahasa Indonesia yang santun dan mudah dipahami. Jangan berikan jawaban yang terlalu panjang.",
                $context['major'] ?? 'Umum',
                $context['career'] ?? 'Belum memilih',
                $context['history_text'] ?? '',
                $context['message'] ?? ''
            ),
            'career_recommendation' => $this->buildCareerRecommendationPrompt($context),
            'pivot_transfer' => sprintf(
                'Tulis 2-3 kalimat yang menghargai keputusan pengguna untuk berpindah karir dari "%s". Mereka telah menyelesaikan %d dari %d skill. Fokuslah pada nilai dari apa yang sudah dipelajari, bukan pada rasa gagal.',
                $context['from_career'] ?? 'karir sebelumnya',
                $context['completed_skills'] ?? 0,
                $context['total_skills'] ?? 0
            ),
            'milestone_25' => sprintf(
                'Tulis 1-2 kalimat singkat penyemangat untuk pengguna yang baru mencapai 25%% progress di karir "%s". Nada: hangat, realistis, tidak berlebihan.',
                $context['career'] ?? 'ini'
            ),
            'milestone_50' => sprintf(
                'Tulis 1-2 kalimat singkat penyemangat untuk pengguna yang sudah 50%% selesai di karir "%s". Nada: momentum, bukan pujian berlebihan.',
                $context['career'] ?? 'ini'
            ),
            'milestone_75' => sprintf(
                'Tulis 1-2 kalimat singkat untuk pengguna yang sudah 75%% selesai di karir "%s". Nada: tenang, hampir selesai, tapi tidak terburu-buru.',
                $context['career'] ?? 'ini'
            ),
            'milestone_100' => sprintf(
                'Tulis 2-3 kalimat untuk pengguna yang baru menyelesaikan seluruh roadmap karir "%s". Nada: reflektif, menghargai proses, membuka babak berikutnya.',
                $context['career'] ?? 'ini'
            ),
            'job_recommendations' => sprintf(
                'Hasilkan data JSON murni berupa array berisi 2 objek rekomendasi pekerjaan di Indonesia untuk karir "%s" dengan domisili di provinsi "%s". Setiap objek harus memiliki key berikut: "title" (nama posisi kerja), "company" (nama perusahaan lokal/multinasional), "location" (nama kota dan provinsi), "salary" (kisaran gaji per bulan dalam format Rp 6.000.000 - Rp 9.000.000), "match" (persentase kecocokan antara %d%% dan 98%%), "source" (sumber info lowongan, misal Glints, Jobstreet, atau Kalibrr). Contoh output format: [{"title": "Junior Software Engineer", "company": "TechIndo", "location": "Jakarta", "salary": "Rp 7.000.000", "match": "85%%", "source": "Glints"}]. Anda HARUS mengembalikan hanya output JSON murni tanpa markdown, tanpa backticks ```json, dan tanpa teks tambahan apa pun.',
                $context['career'] ?? 'Professional',
                $context['province'] ?? 'Indonesia',
                min(95, ($context['crs'] ?? 0) + 10)
            ),
            'assessment_guidance' => sprintf(
                'Tulis 2-3 kalimat pengarahan pembimbing karir yang hangat, suportif, dan personal untuk pengguna sebelum memulai kuesioner asesmen karir. Sesuaikan pengarahan ini dengan profil mereka: jurusan kuliah "%s", tingkat pendidikan terakhir "%s", dan pengalaman kerja "%s". Katakan bahwa kuesioner ini akan menganalisis potensi terbaik mereka.',
                $context['major'] ?? 'Umum',
                $context['education_level'] ?? 'Lulusan SMA/Kuliah',
                $context['work_experience'] ?? 'Belum bekerja'
            ),
            'roadmap_guidance' => sprintf(
                'Tulis 2-3 kalimat arahan dan bimbingan belajar karir hangat dan suportif untuk pengguna yang sedang mempelajari roadmap karir "%s". Progres saat ini adalah %d%% (%d dari %d skill selesai). Sesuaikan saran Anda dengan latar belakang jurusan kuliah mereka: "%s". Berikan saran langkah konkret berikutnya.',
                $context['career'] ?? 'Karir Pilihan',
                $context['crs'] ?? 0,
                $context['completed_skills'] ?? 0,
                $context['total_skills'] ?? 0,
                $context['major'] ?? 'Umum'
            ),
            default => 'Berikan 1-2 kalimat penyemangat netral tentang perjalanan karir.',
        };
    }

    private function topRiasec(array $scores): string
    {
        if (empty($scores)) return 'tidak diketahui';
        arsort($scores);
        $top = array_slice(array_keys($scores), 0, 2);
        return implode(' dan ', $top);
    }

    /**
     * Build a context-aware career recommendation prompt that differentiates
     * between major-aligned recommendations and pivot/transition paths.
     */
    private function buildCareerRecommendationPrompt(array $context): string
    {
        $topRiasec = $this->topRiasec($context['riasec'] ?? []);
        $major     = $context['major'] ?? 'Umum';

        return sprintf(
            "Anda adalah pakar psikologi karir dan penasihat karir profesional. Tugas Anda adalah memberikan narasi motivasi pembimbing karir yang hangat serta merekomendasikan TEPAT 3 karir/pekerjaan yang relevan bagi pengguna berdasarkan hasil asesmen RIASEC mereka.\n\n".
            "Profil Pengguna:\n".
            "- Jurusan Kuliah: %s\n".
            "- Skor RIASEC Tertinggi: %s\n\n".
            "Ketentuan Karir/Pekerjaan yang Direkomendasikan:\n".
            "1. Rekomendasi karir harus universal dan realistis (sesuaikan dengan jurusan kuliah mereka dan hasil minat RIASEC). Misalnya, jika jurusan mereka adalah Pendidikan Guru Sekolah Dasar / PGSD, berikan rekomendasi pekerjaan seperti Guru Sekolah Dasar, Guru Bimbel/Tutor, Penulis Buku Parenting/Edukasi, Staf Administrasi Publik (PNS), dll. Jangan batasi hanya pada pekerjaan IT, kecuali jika jurusan mereka adalah rumpun IT/STEM atau skor RIASEC-nya sangat condong ke sana.\n".
            "2. Setiap rekomendasi karir harus memiliki daftar 6-8 skill spesifik (terbagi merata ke level 'beginner', 'intermediate', dan 'advanced') beserta estimasi jam belajar ('estimated_hours', antara 5 hingga 60) dan urutan ('order', mulai dari 1) agar sistem dapat membuat kurikulum belajar otomatis bagi mereka.\n\n".
            "Anda HARUS mengembalikan data dalam format JSON murni dengan struktur persis seperti berikut (jangan sertakan markdown, backticks ```json, atau penjelasan lain di luar JSON):\n".
            "{\n".
            "  \"narrative\": \"Narasi motivasi yang hangat, suportif, dan tidak menghakimi sepanjang 2-3 kalimat dalam Bahasa Indonesia yang menjelaskan mengapa rekomendasi ini cocok untuk mereka.\",\n".
            "  \"careers\": [\n".
            "    {\n".
            "      \"name\": \"Nama Karir/Pekerjaan (misal: Guru Sekolah Dasar)\",\n".
            "      \"description\": \"Deskripsi singkat tentang karir ini dalam 2 kalimat.\",\n".
            "      \"riasec_code\": \"Kode RIASEC 3-huruf yang dominan (misal: SAE)\",\n".
            "      \"industry_standard\": \"Nama Industri/Kategori (misal: Pendidikan)\",\n".
            "      \"match_percent\": 90,\n".
            "      \"reasons\": [\n".
            "        \"Alasan pertama kecocokan berdasarkan skor RIASEC atau kepribadian.\",\n".
            "        \"Alasan kedua kecocokan berdasarkan keselarasan/transisi dari jurusan %s.\"\n".
            "      ],\n".
            "      \"cautions\": [\n".
            "        \"Hal yang perlu dipertimbangkan atau dikembangkan jika ada (misal: memerlukan sertifikasi mengajar).\"\n".
            "      ],\n".
            "      \"is_major_match\": true,\n".
            "      \"skills\": [\n".
            "        {\n".
            "          \"name\": \"Nama skill 1 (misal: Manajemen Kelas)\",\n".
            "          \"level\": \"beginner\",\n".
            "          \"estimated_hours\": 20,\n".
            "          \"order\": 1\n".
            "        },\n".
            "        {\n".
            "          \"name\": \"Nama skill 2 (misal: Pengembangan RPP)\",\n".
            "          \"level\": \"intermediate\",\n".
            "          \"estimated_hours\": 25,\n".
            "          \"order\": 2\n".
            "        },\n".
            "        {\n".
            "          \"name\": \"Nama skill 3 (misal: Evaluasi Pembelajaran)\",\n".
            "          \"level\": \"advanced\",\n".
            "          \"estimated_hours\": 30,\n".
            "          \"order\": 3\n".
            "        }\n".
            "      ]\n".
            "    }\n".
            "  ]\n".
            "}",
            $major,
            $topRiasec,
            $major
        );
    }

    /* ── Rule-based Fallback ──────────────────────────────── */

    private function ruleBasedFallback(string $type, array $context): string
    {
        return match ($type) {
            'roadmap_generation'    => json_encode([
                [
                    'name' => 'Keterampilan Dasar ' . ($context['career_name'] ?? 'Karir'),
                    'level' => 'beginner',
                    'estimated_hours' => 20,
                    'order' => 1,
                    'validation_type' => 'reflection',
                    'scenario_question' => null,
                ],
                [
                    'name' => 'Keterampilan Menengah ' . ($context['career_name'] ?? 'Karir'),
                    'level' => 'intermediate',
                    'estimated_hours' => 30,
                    'order' => 2,
                    'validation_type' => 'scenario',
                    'scenario_question' => 'Bagaimana Anda menerapkan konsep menengah ini dalam pekerjaan sehari-hari?',
                ],
                [
                    'name' => 'Keterampilan Lanjutan ' . ($context['career_name'] ?? 'Karir'),
                    'level' => 'advanced',
                    'estimated_hours' => 40,
                    'order' => 3,
                    'validation_type' => 'behavior',
                    'scenario_question' => null,
                ]
            ]),
            'chatbot_response'      => 'Maaf, saya sedang tidak dapat terhubung ke server kecerdasan buatan. Namun, Anda dapat menanyakan tentang roadmap belajar, sertifikasi, atau tips karir lainnya setelah koneksi pulih.',
            'custom_questions'      => json_encode($context['questions'] ?? []),
            'career_recommendation' => json_encode([
                'narrative' => "Arah ini muncul berdasarkan pola jawabanmu. Kamu tetap memegang keputusan akhirnya.",
                'careers' => [
                    [
                        'name' => 'Konsultan Pendidikan',
                        'description' => 'Membantu lembaga pendidikan dalam meningkatkan kualitas kurikulum dan metode pengajaran.',
                        'riasec_code' => 'SAE',
                        'industry_standard' => 'Pendidikan',
                        'match_percent' => 90,
                        'reasons' => [
                            'Menyelaraskan kemampuan mengajar dengan rancangan program belajar.',
                            'Sesuai dengan skor minat Sosial yang tinggi.'
                        ],
                        'cautions' => [],
                        'is_major_match' => true,
                        'skills' => [
                            ['name' => 'Manajemen Kelas & Kondusivitas', 'level' => 'beginner', 'estimated_hours' => 20, 'order' => 1],
                            ['name' => 'Pengembangan Kurikulum & RPP', 'level' => 'intermediate', 'estimated_hours' => 25, 'order' => 2],
                            ['name' => 'Psikologi Perkembangan Anak', 'level' => 'advanced', 'estimated_hours' => 30, 'order' => 3],
                        ]
                    ],
                    [
                        'name' => 'Tutor / Guru Bimbel',
                        'description' => 'Mengajar dan membantu siswa memahami materi akademis sekolah di lembaga bimbingan belajar secara interaktif.',
                        'riasec_code' => 'SEC',
                        'industry_standard' => 'Pendidikan & Pelatihan',
                        'match_percent' => 85,
                        'reasons' => [
                            'Mengajar dan membantu siswa memahami materi akademis secara menyenangkan.',
                            'Mengembangkan keterampilan instruksional praktis.'
                        ],
                        'cautions' => [],
                        'is_major_match' => true,
                        'skills' => [
                            ['name' => 'Teknik Mengajar Interaktif & Seru', 'level' => 'beginner', 'estimated_hours' => 15, 'order' => 1],
                            ['name' => 'Pemahaman Kurikulum & Bank Soal', 'level' => 'intermediate', 'estimated_hours' => 20, 'order' => 2],
                            ['name' => 'Diagnosis Kesulitan Belajar Siswa', 'level' => 'advanced', 'estimated_hours' => 25, 'order' => 3],
                        ]
                    ],
                    [
                        'name' => 'Penulis Buku Parenting & Edukasi',
                        'description' => 'Menulis artikel, buku, dan konten edukatif seputar pola asuh anak dan teknik pembelajaran kreatif.',
                        'riasec_code' => 'ASE',
                        'industry_standard' => 'Media & Penerbitan',
                        'match_percent' => 80,
                        'reasons' => [
                            'Menggabungkan ekspresi ide dengan penyampaian edukatif.',
                            'Cocok untuk minat Artistik dan Sosial.'
                        ],
                        'cautions' => [],
                        'is_major_match' => true,
                        'skills' => [
                            ['name' => 'Keterampilan Menulis Kreatif', 'level' => 'beginner', 'estimated_hours' => 20, 'order' => 1],
                            ['name' => 'Riset & Teori Psikologi Ibu-Anak', 'level' => 'intermediate', 'estimated_hours' => 25, 'order' => 2],
                            ['name' => 'Copywriting & Content Creation', 'level' => 'advanced', 'estimated_hours' => 30, 'order' => 3],
                        ]
                    ]
                ]
            ], JSON_UNESCAPED_UNICODE),
            'pivot_transfer'        => 'Yang sudah kamu pelajari tetap bernilai. Perjalanan sebelumnya tidak hilang.',
            'milestone_25'          => 'Kamu sudah menyelesaikan seperempat perjalanan. Langkah kecil yang konsisten adalah fondasi yang kuat.',
            'milestone_50'          => 'Setengah perjalanan sudah kamu tempuh. Ini bukan titik tengah — ini momentum.',
            'milestone_75'          => 'Sudah tiga perempat jalan. Yang tersisa adalah finalisasi, bukan perjuangan baru.',
            'milestone_100'         => 'Roadmap ini selesai. Tapi belajar tidak pernah benar-benar selesai — ini hanya satu babak.',
            'job_recommendations'   => json_encode([
                [
                    'title' => 'Junior ' . ($context['career'] ?? 'Specialist'),
                    'company' => 'Perusahaan Mitra Suluh',
                    'location' => $context['province'] ?? 'Indonesia',
                    'salary' => 'Rp 5.000.000 - Rp 7.000.000',
                    'match' => '80%',
                    'source' => 'Suluh Network'
                ],
                [
                    'title' => ($context['career'] ?? 'Specialist') . ' Magang',
                    'company' => 'Startup Lokal',
                    'location' => 'Remote',
                    'salary' => 'Rp 2.500.000 - Rp 4.000.000',
                    'match' => '90%',
                    'source' => 'Suluh Network'
                ]
            ]),
            'assessment_guidance'   => 'Selamat datang di asesmen karir Suluh! Kuesioner ini akan membantumu memahami keselarasan minat dan kepribadianmu dengan berbagai jalur karir profesional. Jawablah setiap pertanyaan secara jujur sesuai dengan kenyamananmu.',
            'roadmap_guidance'      => 'Perjalanan belajarmu sedang berlangsung. Fokuslah pada penyelesaian setiap skill secara bertahap, mulai dari tingkat Fondasi hingga Lanjutan. Konsistensi langkah kecil adalah kunci utama kesuksesanmu.',
            default                 => 'Perjalananmu berlanjut.',
        };
    }

    /**
     * Extracts and cleans valid JSON strings from LLM text responses
     * that might contain markdown formatting, code blocks, or conversational preambles/postambles.
     */
    private function cleanJsonString(string $text): string
    {
        $text = trim($text);

        // Try standard markdown code block removal first
        if (str_starts_with($text, '```')) {
            $text = preg_replace('/^```(?:json)?\n?|```$/', '', $text);
            $text = trim($text);
        }

        // Find the first '[' and last ']' for array JSON, or '{' and '}' for object JSON
        $firstBracket = strpos($text, '[');
        $lastBracket = strrpos($text, ']');
        
        $firstBrace = strpos($text, '{');
        $lastBrace = strrpos($text, '}');
        
        if ($firstBracket !== false && $lastBracket !== false && ($firstBrace === false || $firstBracket < $firstBrace)) {
            return substr($text, $firstBracket, $lastBracket - $firstBracket + 1);
        } elseif ($firstBrace !== false && $lastBrace !== false) {
            return substr($text, $firstBrace, $lastBrace - $firstBrace + 1);
        }
        
        return $text;
    }
}
