<?php

namespace App\Services;

use App\Models\LlmNarrativeCache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LLMNarrativeService
{
    /**
     * Fallback chain: Gemini → Groq → rule-based template.
     * Hasil di-cache per (user_id, type, profile_hash).
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

        [$narrative, $provider] = $this->tryGemini($type, $context)
            ?? $this->tryGroq($type, $context)
            ?? [$this->ruleBasedFallback($type, $context), 'rule_based'];

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
        $apiKey = config('services.gemini.api_key');
        if (! $apiKey) return null;

        try {
            $response = Http::timeout(10)->post(
                config('services.gemini.endpoint') . '?key=' . $apiKey,
                [
                    'contents' => [
                        ['parts' => [['text' => $this->buildPrompt($type, $context)]]]
                    ],
                    'generationConfig' => [
                        'maxOutputTokens' => 200,
                        'temperature'     => 0.7,
                    ],
                ]
            );

            if ($response->successful()) {
                $text = $response->json('candidates.0.content.parts.0.text');
                if ($text) {
                    return [trim($text), 'gemini'];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('LLM Gemini failed: ' . $e->getMessage());
        }

        return null;
    }

    /* ── Groq (llama3) ────────────────────────────────────── */

    private function tryGroq(string $type, array $context): ?array
    {
        $apiKey = config('services.groq.api_key');
        if (! $apiKey) return null;

        try {
            $response = Http::timeout(10)
                ->withToken($apiKey)
                ->post(config('services.groq.endpoint'), [
                    'model'       => config('services.groq.model', 'llama3-8b-8192'),
                    'messages'    => [
                        ['role' => 'system', 'content' => 'Kamu adalah asisten karir yang empatik, ringkas, dan tidak menghakimi. Tulis dalam Bahasa Indonesia. Maksimal 3 kalimat.'],
                        ['role' => 'user',   'content' => $this->buildPrompt($type, $context)],
                    ],
                    'max_tokens'  => 200,
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                $text = $response->json('choices.0.message.content');
                if ($text) {
                    return [trim($text), 'groq'];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('LLM Groq failed: ' . $e->getMessage());
        }

        return null;
    }

    /* ── Prompt Builder ───────────────────────────────────── */

    private function buildPrompt(string $type, array $context): string
    {
        return match ($type) {
            'career_recommendation' => sprintf(
                'Berikan 2-3 kalimat penyemangat yang netral dan tidak menghakimi untuk pengguna yang baru menyelesaikan asesmen karir. Skor RIASEC tertinggi mereka: %s. Karir teratas yang muncul: %s. Jangan menyebutkan nama karir secara langsung — fokuslah pada perjalanan dan otonomi pengguna.',
                $this->topRiasec($context['riasec'] ?? []),
                implode(', ', array_slice($context['top'] ?? [], 0, 2))
            ),
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

    /* ── Rule-based Fallback ──────────────────────────────── */

    private function ruleBasedFallback(string $type, array $context): string
    {
        return match ($type) {
            'career_recommendation' => 'Arah ini muncul berdasarkan pola jawabanmu. Kamu tetap memegang keputusan akhirnya.',
            'pivot_transfer'        => 'Yang sudah kamu pelajari tetap bernilai. Perjalanan sebelumnya tidak hilang.',
            'milestone_25'          => 'Kamu sudah menyelesaikan seperempat perjalanan. Langkah kecil yang konsisten adalah fondasi yang kuat.',
            'milestone_50'          => 'Setengah perjalanan sudah kamu tempuh. Ini bukan titik tengah — ini momentum.',
            'milestone_75'          => 'Sudah tiga perempat jalan. Yang tersisa adalah finalisasi, bukan perjuangan baru.',
            'milestone_100'         => 'Roadmap ini selesai. Tapi belajar tidak pernah benar-benar selesai — ini hanya satu babak.',
            default                 => 'Perjalananmu berlanjut.',
        };
    }
}
