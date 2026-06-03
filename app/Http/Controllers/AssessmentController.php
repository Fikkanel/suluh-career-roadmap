<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Eloquent\AssessmentRepository;
use App\Services\ScoringService;
use App\Services\LLMNarrativeService;

class AssessmentController extends Controller
{
    public function __construct(
        private readonly AssessmentRepository  $assessmentRepo,
        private readonly ScoringService        $scoring,
        private readonly LLMNarrativeService   $llm,
    ) {}

    public function show()
    {
        $user = auth()->user();

        // If the user already completed the assessment, redirect to results
        // so they don't see the questionnaire again when navigating the app.
        // Allow retaking only when explicitly triggered (e.g., pivot flow).
        if (!session('pivot')) {
            $existingResult = $this->assessmentRepo->getLatestResult($user->id);
            if ($existingResult) {
                return redirect()->route('assessment.result');
            }
        }

        $questions = $this->assessmentRepo->getActiveQuestions();

        if (empty($questions)) {
            $questions = [];
        }

        $major = $user->major ?? '';

        if (!empty($major) && strtolower(trim($major)) !== 'umum' && !empty($questions) && !app()->environment('testing')) {
            try {
                $minimalQuestions = collect($questions)->map(fn($q) => [
                    'id'      => $q['id'],
                    'prompt'  => $q['prompt'],
                    'type'    => $q['type'],
                    'options' => $q['options'] ?? null,
                ])->toArray();

                $customQuestionsJson = $this->llm->generate($user->id, 'custom_questions', [
                    'major' => $major,
                    'questions' => $minimalQuestions,
                ]);
                $customQuestions = json_decode($customQuestionsJson, true);
                if (is_array($customQuestions) && count($customQuestions) === count($questions)) {
                    $mergedQuestions = [];
                    foreach ($customQuestions as $cq) {
                        $orig = collect($questions)->firstWhere('id', $cq['id'] ?? null);
                        if ($orig) {
                            $mergedQuestions[] = [
                                'id' => $orig['id'],
                                'prompt' => $cq['prompt'] ?? $orig['prompt'],
                                'context' => $cq['context'] ?? $orig['context'] ?? '',
                                'riasec_category' => $orig['riasec_category'],
                                'big_five_trait' => $orig['big_five_trait'],
                                'weight' => $orig['weight'],
                                'type' => $orig['type'],
                                'options' => ($orig['type'] === 'single_choice') ? ($cq['options'] ?? $orig['options']) : null,
                            ];
                        }
                    }
                    if (count($mergedQuestions) === count($questions)) {
                        $questions = $mergedQuestions;
                    }
                }
            } catch (\Throwable $e) {
                logger()->error('Failed to generate customized assessment questions: ' . $e->getMessage());
            }
        }

        $total = count($questions);
        $step  = 1;

        // Restore draft answers from session if available
        $savedAnswers = session('assessment_draft', []);

        $guidance = $this->llm->generate($user->id, 'assessment_guidance', [
            'major'           => $user->major ?? 'Umum',
            'education_level' => $user->education_level ?? 'SMA/Kuliah',
            'work_experience' => $user->work_experience ?? 'Belum ada',
        ]);

        return view('app.assessment', compact('questions', 'total', 'step', 'savedAnswers', 'guidance'));
    }

    /**
     * Save current answers as a draft in session so the user can resume later.
     */
    public function saveDraft(Request $request)
    {
        $questions = $this->assessmentRepo->getActiveQuestions();

        $rules = [];
        foreach ($questions as $q) {
            if ($q['type'] === 'scale') {
                $rules['answers.' . $q['id']] = 'nullable|integer|min:1|max:10';
            } elseif ($q['type'] === 'single_choice') {
                $rules['answers.' . $q['id']] = 'nullable|string|in:a,b,c,d';
            } else {
                $rules['answers.' . $q['id']] = 'nullable|string|max:2000';
            }
        }
        $validated = $request->validate($rules);
        $answers   = $validated['answers'] ?? [];

        // Filter out empty/null answers before saving
        $answers = array_filter($answers, fn ($v) => $v !== null && $v !== '');

        session(['assessment_draft' => $answers]);

        return redirect()->route('assessment')
            ->with('success', 'Jawaban berhasil disimpan. Kamu bisa melanjutkan kapan saja.');
    }

    public function submit(Request $request)
    {
        $questions = $this->assessmentRepo->getActiveQuestions();

        $rules = [];
        foreach ($questions as $q) {
            if ($q['type'] === 'scale') {
                $rules['answers.' . $q['id']] = 'nullable|integer|min:1|max:10';
            } elseif ($q['type'] === 'single_choice') {
                $rules['answers.' . $q['id']] = 'nullable|string|in:a,b,c,d';
            } else {
                $rules['answers.' . $q['id']] = 'nullable|string|max:2000';
            }
        }
        $validated = $request->validate($rules);
        $answers   = $validated['answers'] ?? [];

        $scores        = $this->scoring->calculateScores($answers, $questions);
        $lockedCareer  = $this->scoring->getLockedCareer($scores['riasec'], auth()->user()->major);
        $topCareerIds  = !empty($lockedCareer) ? [$lockedCareer['id']] : [];

        $result = $this->assessmentRepo->saveResult(
            auth()->id(),
            $scores,
            $topCareerIds
        );

        session(['assessment_result_id' => $result->id]);

        // Clear draft after successful submission
        session()->forget('assessment_draft');

        $this->llm->generate(auth()->id(), 'career_recommendation', [
            'riasec'         => $scores['riasec'],
            'big_five'       => $scores['big_five'],
            'top'            => $topCareerIds,
            'major'          => auth()->user()->major,
            'is_major_match' => $lockedCareer['is_major_match'] ?? false,
            'career_name'    => $lockedCareer['name'] ?? '',
        ]);

        return redirect()->route('assessment.result');
    }

    public function result()
    {
        $result = $this->assessmentRepo->getLatestResult(auth()->id());

        if (! $result) {
            return redirect()->route('assessment');
        }

        $scores       = $result->riasec_scores ?? [];
        $lockedCareer = $this->scoring->getLockedCareer($scores, auth()->user()->major);

        $narrativeData = $this->llm->generate(auth()->id(), 'career_recommendation', [
            'riasec'         => $scores,
            'big_five'       => $result->big_five_scores ?? [],
            'top'            => !empty($lockedCareer) ? [$lockedCareer['id']] : [],
            'major'          => auth()->user()->major,
            'is_major_match' => $lockedCareer['is_major_match'] ?? false,
            'career_name'    => $lockedCareer['name'] ?? '',
        ]);

        $decoded = json_decode($narrativeData, true);
        $narrativeText = '';
        $recommendations = [];

        if ($decoded && isset($decoded['careers'])) {
            $narrativeText = $decoded['narrative'] ?? '';
            foreach ($decoded['careers'] as $c) {
                // Determine matches major
                $isMajorMatch = $c['is_major_match'] ?? false;
                
                // Save or update career in DB dynamically
                $slug = \Illuminate\Support\Str::slug($c['name']);
                $dbCareer = \App\Models\Career::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => $c['name'],
                        'description' => $c['description'] ?? '',
                        'riasec_code' => $c['riasec_code'] ?? 'IRA',
                        'industry_standard' => $c['industry_standard'] ?? 'Umum',
                        'is_active' => true,
                    ]
                );

                // Save or update skills
                if (!empty($c['skills'])) {
                    foreach ($c['skills'] as $s) {
                        \App\Models\Skill::updateOrCreate(
                            [
                                'career_id' => $dbCareer->id,
                                'name' => $s['name'],
                            ],
                            [
                                'level' => $s['level'] ?? 'beginner',
                                'estimated_hours' => $s['estimated_hours'] ?? 10,
                                'order' => $s['order'] ?? 1,
                            ]
                        );
                    }
                }

                $recommendations[] = [
                    'id' => $dbCareer->id,
                    'name' => $dbCareer->name,
                    'description' => $dbCareer->description,
                    'riasec_code' => $dbCareer->riasec_code,
                    'industry_standard' => $dbCareer->industry_standard,
                    'matchPercent' => $c['match_percent'] ?? 80,
                    'reasons' => $c['reasons'] ?? [],
                    'cautions' => $c['cautions'] ?? [],
                    'is_major_match' => $isMajorMatch,
                ];
            }
        } else {
            // Fallback: Parse narrative if it's text or get top careers from database
            $narrativeText = $narrativeData;
            // Get top careers from database matching the user's major
            $recommendations = $this->scoring->getTopCareers($scores, 3, auth()->user()->major);
        }

        return view('app.assessment-result', [
            'recommendations' => $recommendations,
            'narrative'       => $narrativeText,
            'riasec'          => $scores,
        ]);
    }
}
