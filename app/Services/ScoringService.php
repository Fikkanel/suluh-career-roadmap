<?php

namespace App\Services;

use App\Models\Career;

class ScoringService
{
    /**
     * Map single_choice option letter to numeric score.
     * a=4 (strongly positive), b=3, c=2, d=1 (strongly negative/avoidant).
     */
    private array $choiceMap = ['a' => 4, 'b' => 3, 'c' => 2, 'd' => 1];

    /**
     * Calculate RIASEC and Big Five scores from raw answers.
     * $answers: ['question_id' => 'value'] where value is option key or numeric string (scale).
     * $questions: array of AssessmentQuestion models/arrays with riasec_category, big_five_trait, type, weight.
     */
    public function calculateScores(array $answers, array $questions): array
    {
        $riasec  = ['R' => 0.0, 'I' => 0.0, 'A' => 0.0, 'S' => 0.0, 'E' => 0.0, 'C' => 0.0];
        $bigFive = ['Openness' => 0.0, 'Conscientiousness' => 0.0, 'Extraversion' => 0.0, 'Agreeableness' => 0.0, 'Neuroticism' => 0.0];
        $riasecCount  = array_fill_keys(array_keys($riasec), 0);
        $bigFiveCount = array_fill_keys(array_keys($bigFive), 0);

        foreach ($questions as $question) {
            $id       = $question['id'];
            $type     = $question['type'];
            $weight   = (float) ($question['weight'] ?? 1.0);
            $category = $question['riasec_category'] ?? null;
            $trait    = $question['big_five_trait'] ?? null;
            $value    = $answers[$id] ?? null;

            if ($value === null) continue;

            $score = match ($type) {
                'single_choice'  => ($this->choiceMap[$value] ?? 0),
                'scale'          => $this->normalizeScale((int) $value),
                'text_reflection'=> 0,
                default          => 0,
            };

            if ($score === 0) continue;

            $weighted = $score * $weight;

            if ($category && isset($riasec[$category])) {
                $riasec[$category]      += $weighted;
                $riasecCount[$category] += $weight;
            }
            if ($trait && isset($bigFive[$trait])) {
                $bigFive[$trait]      += $weighted;
                $bigFiveCount[$trait] += $weight;
            }
        }

        foreach ($riasec as $k => $v) {
            $riasec[$k] = $riasecCount[$k] > 0
                ? round($v / ($riasecCount[$k] * 4) * 100)
                : 0;
        }
        foreach ($bigFive as $k => $v) {
            $bigFive[$k] = $bigFiveCount[$k] > 0
                ? round($v / ($bigFiveCount[$k] * 4) * 100)
                : 0;
        }

        return ['riasec' => $riasec, 'big_five' => $bigFive];
    }

    /**
     * Normalize a 1–10 scale value to 0.25–4.0 range.
     */
    private function normalizeScale(int $value): float
    {
        $clamped = max(1, min(10, $value));
        return round($clamped / 10 * 4, 2);
    }

    /**
     * Return top N careers sorted by match score against user's RIASEC vector.
     * Each career's RIASEC code (e.g. "IRA") is decoded into a weighted vector:
     * 1st letter = 3pts, 2nd = 2pts, 3rd = 1pt.
     */
    public function getTopCareers(array $riasecScores, int $limit = 3, ?string $major = null): array
    {
        $careers = Career::where('is_active', true)->with('skills')->get();
        $majorLower = $major ? strtolower(trim($major)) : '';

        $scored = $careers->map(function (Career $career) use ($riasecScores, $majorLower) {
            $careerVector = $this->riasecCodeToVector($career->riasec_code);
            $dotProduct   = 0.0;
            $careerNorm   = 0.0;
            $userNorm     = 0.0;

            foreach ($careerVector as $letter => $weight) {
                $userScore   = $riasecScores[$letter] ?? 0;
                $dotProduct += $userScore * $weight;
                $careerNorm += $weight ** 2;
                $userNorm   += $userScore ** 2;
            }

            $denom = sqrt($careerNorm) * sqrt($userNorm);
            $similarity = $denom > 0 ? $dotProduct / $denom : 0;
            $matchPercent = (int) round(min(100, max(0, $similarity * 100)));

            $majorMatchBonus = $this->calculateMajorMatchBonus($career, $majorLower);
            $totalScore = $matchPercent + $majorMatchBonus;

            return [
                'career'       => $career,
                'match_percent'=> $matchPercent,
                'total_score'  => $totalScore,
                'is_major_match' => ($majorMatchBonus >= 40),
            ];
        });

        return $scored
            ->sortByDesc('total_score')
            ->take($limit)
            ->map(fn ($item) => [
                'id'           => $item['career']->id,
                'name'         => $item['career']->name,
                'description'  => $item['career']->description,
                'riasec_code'  => $item['career']->riasec_code,
                'industry_standard' => $item['career']->industry_standard,
                'matchPercent' => $item['match_percent'],
                'reasons'      => $this->buildReasons($item['career'], $riasecScores),
                'cautions'     => [],
                'is_major_match' => $item['is_major_match'],
            ])
            ->values()
            ->toArray();
    }

    /**
     * Decode a RIASEC code string into a weighted letter map.
     * "IRA" → ['I'=>3, 'R'=>2, 'A'=>1]
     */
    private function riasecCodeToVector(string $code): array
    {
        $letters = str_split(strtoupper($code));
        $weights = [3, 2, 1];
        $vector  = [];
        foreach ($letters as $i => $letter) {
            if (isset($weights[$i]) && in_array($letter, ['R','I','A','S','E','C'])) {
                $vector[$letter] = $weights[$i];
            }
        }
        return $vector;
    }

    /**
     * Build human-readable reason strings for a career match.
     */
    private function buildReasons(Career $career, array $riasecScores): array
    {
        $labels = [
            'R' => 'realistik (suka hal konkret & teknis)',
            'I' => 'investigatif (suka analisis & eksplorasi)',
            'A' => 'artistik (suka kreativitas & ekspresi)',
            'S' => 'sosial (suka membantu & mengajar)',
            'E' => 'enterprising (suka memimpin & meyakinkan)',
            'C' => 'konvensional (suka keteraturan & prosedur)',
        ];
        $letters = str_split(strtoupper($career->riasec_code));
        $reasons = [];
        foreach (array_slice($letters, 0, 2) as $letter) {
            if (isset($labels[$letter]) && ($riasecScores[$letter] ?? 0) >= 40) {
                $reasons[] = 'Pola jawabanmu menunjukkan kecenderungan ' . $labels[$letter];
            }
        }
        if (empty($reasons)) {
            $reasons[] = 'Pola jawaban asesmen kamu selaras dengan kebutuhan karir ini';
        }
        return $reasons;
    }

    /**
     * Get a single locked career based on RIASEC score matching and the user's major.
     */
    public function getLockedCareer(array $riasecScores, ?string $major): array
    {
        $careers = Career::where('is_active', true)->with('skills')->get();
        $majorLower = $major ? strtolower(trim($major)) : '';

        $scored = $careers->map(function (Career $career) use ($riasecScores, $majorLower) {
            $careerVector = $this->riasecCodeToVector($career->riasec_code);
            $dotProduct   = 0.0;
            $careerNorm   = 0.0;
            $userNorm     = 0.0;

            foreach ($careerVector as $letter => $weight) {
                $userScore   = $riasecScores[$letter] ?? 0;
                $dotProduct += $userScore * $weight;
                $careerNorm += $weight ** 2;
                $userNorm   += $userScore ** 2;
            }

            $denom = sqrt($careerNorm) * sqrt($userNorm);
            $similarity = $denom > 0 ? $dotProduct / $denom : 0;
            $matchPercent = (int) round(min(100, max(0, $similarity * 100)));

            $majorMatchBonus = $this->calculateMajorMatchBonus($career, $majorLower);
            $totalScore = $matchPercent + $majorMatchBonus;

            return [
                'career'       => $career,
                'match_percent'=> $matchPercent,
                'total_score'  => $totalScore,
            ];
        });

        // Get the single top career based on total score
        $best = $scored->sortByDesc('total_score')->first();

        if (!$best) {
            $fallbackCareer = Career::where('is_active', true)->first();
            if (!$fallbackCareer) {
                return [];
            }
            return [
                'id'           => $fallbackCareer->id,
                'name'         => $fallbackCareer->name,
                'description'  => $fallbackCareer->description,
                'riasec_code'  => $fallbackCareer->riasec_code,
                'industry_standard' => $fallbackCareer->industry_standard,
                'matchPercent' => 100,
                'reasons'      => ['Karir default terpilih'],
                'cautions'     => [],
                'is_major_match' => false,
            ];
        }

        $career = $best['career'];
        $majorMatchBonus = $this->calculateMajorMatchBonus($career, $majorLower);
        return [
            'id'           => $career->id,
            'name'         => $career->name,
            'description'  => $career->description,
            'riasec_code'  => $career->riasec_code,
            'industry_standard' => $career->industry_standard,
            'matchPercent' => $best['match_percent'],
            'reasons'      => $this->buildReasons($career, $riasecScores),
            'cautions'     => [],
            'is_major_match' => ($majorMatchBonus >= 40),
        ];
    }

    private function calculateMajorMatchBonus(Career $career, string $majorLower): int
    {
        $majorMatchBonus = 0;
        if ($majorLower !== '') {
            $matchesMajor = false;
            $careerSlug = $career->slug;

            if ($careerSlug === 'software-engineer') {
                $matchesMajor = $this->hasAnyKeyword($majorLower, ['informatika', 'komputer', 'ti', 'software', 'perangkat lunak', 'rpl', 'it', 'web', 'programmer', 'development']);
            } elseif ($careerSlug === 'ux-designer') {
                $matchesMajor = $this->hasAnyKeyword($majorLower, ['desain', 'dkv', 'seni', 'visual', 'grafis', 'ux', 'ui', 'psikologi', 'interaksi', 'interaction']);
            } elseif ($careerSlug === 'data-analyst') {
                $matchesMajor = $this->hasAnyKeyword($majorLower, ['data', 'statistika', 'matematika', 'analyst', 'sains data', 'akuntansi', 'keuangan', 'finance', 'aktuaria', 'ekonomi']);
            } elseif ($careerSlug === 'digital-marketer') {
                $matchesMajor = $this->hasAnyKeyword($majorLower, ['marketing', 'pemasaran', 'komunikasi', 'humas', 'pr', 'bisnis', 'manajemen', 'periklanan', 'advertising']);
            } elseif ($careerSlug === 'product-manager') {
                $matchesMajor = $this->hasAnyKeyword($majorLower, ['manajemen', 'bisnis', 'proyek', 'project', 'sistem informasi', 'si', 'it', 'komputer', 'informatika']);
            } elseif ($careerSlug === 'cybersecurity-analyst') {
                $matchesMajor = $this->hasAnyKeyword($majorLower, ['keamanan', 'cyber', 'cybersecurity', 'jaringan', 'network', 'komputer', 'it', 'informatika', 'sistem komputer']);
            } elseif ($careerSlug === 'business-analyst') {
                $matchesMajor = $this->hasAnyKeyword($majorLower, ['bisnis', 'ekonomi', 'manajemen', 'akuntansi', 'finance', 'keuangan', 'analyst', 'sistem informasi', 'si']);
            } elseif ($careerSlug === 'cloud-engineer') {
                $matchesMajor = $this->hasAnyKeyword($majorLower, ['cloud', 'jaringan', 'network', 'infrastruktur', 'komputer', 'it', 'informatika', 'sistem komputer']);
            } elseif ($careerSlug === 'guru-sd') {
                $matchesMajor = $this->hasAnyKeyword($majorLower, ['pgsd', 'pendidikan', 'guru', 'paud', 'tarbiyah', 'keguruan', 'pembelajaran']);
            } elseif ($careerSlug === 'guru-bimbel') {
                $matchesMajor = $this->hasAnyKeyword($majorLower, ['pendidikan', 'guru', 'paud', 'tarbiyah', 'keguruan', 'sastra', 'bahasa', 'matematika', 'fisika', 'kimia', 'biologi', 'sejarah', 'geografi']);
            } elseif ($careerSlug === 'penulis-parenting') {
                $matchesMajor = $this->hasAnyKeyword($majorLower, ['psikologi', 'sastra', 'bahasa', 'komunikasi', 'pendidikan', 'guru', 'jurnalistik', 'humas', 'parenting']);
            } elseif ($careerSlug === 'pns-adm') {
                $matchesMajor = $this->hasAnyKeyword($majorLower, ['administrasi', 'pemerintahan', 'hukum', 'politik', 'sosial', 'manajemen', 'ekonomi', 'kebijakan']);
            }

            if ($matchesMajor) {
                $majorMatchBonus = 40; // Direct major match
            } else {
                // Stricter alignment logic for transition/pivot candidates
                $isItStemMajor = $this->hasAnyKeyword($majorLower, ['informatika', 'komputer', 'ti', 'software', 'perangkat lunak', 'rpl', 'it', 'web', 'programmer', 'development', 'data', 'statistika', 'matematika', 'sains data', 'teknik', 'engineering', 'sistem informasi', 'si', 'aktuaria']);
                
                // Technical barrier penalty for non-IT/STEM majors targeting heavy technical roles
                if (in_array($careerSlug, ['software-engineer', 'cloud-engineer', 'cybersecurity-analyst']) && !$isItStemMajor) {
                    $majorMatchBonus = -30;
                }
                
                // Creative/humanist boost for creative/social digital careers (UX Designer, Digital Marketer)
                $isHumanistCreativeMajor = $this->hasAnyKeyword($majorLower, ['pendidikan', 'guru', 'pgsd', 'seni', 'desain', 'visual', 'dkv', 'komunikasi', 'humas', 'pr', 'sastra', 'bahasa', 'psikologi', 'sosial', 'sejarah', 'filsafat', 'hubungan internasional', 'hi']);
                if (in_array($careerSlug, ['ux-designer', 'digital-marketer']) && $isHumanistCreativeMajor) {
                    $majorMatchBonus = 20;
                }

                // Strategic management boost for Product Manager or Business Analyst
                $isManagementStrategicMajor = $this->hasAnyKeyword($majorLower, ['manajemen', 'bisnis', 'ekonomi', 'pendidikan', 'guru', 'pgsd', 'akuntansi', 'finance', 'keuangan', 'administrasi']);
                if (in_array($careerSlug, ['product-manager', 'business-analyst']) && $isManagementStrategicMajor && $majorMatchBonus === 0) {
                    $majorMatchBonus = 15;
                }
            }
        }
        return $majorMatchBonus;
    }

    private function hasAnyKeyword(string $text, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains($text, $keyword)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Calculate Career Readiness Score (CRS) for a user.
     */
    public function calculateCrs(int $doneSkills, int $totalSkills): int
    {
        if ($totalSkills === 0) return 0;
        return (int) round($doneSkills / $totalSkills * 100);
    }
}
