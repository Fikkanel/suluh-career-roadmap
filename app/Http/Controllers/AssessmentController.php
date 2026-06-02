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
        $questions = $this->assessmentRepo->getActiveQuestions();

        if (empty($questions)) {
            $questions = [];
        }

        $total = count($questions);
        $step  = 1;

        // Restore draft answers from session if available
        $savedAnswers = session('assessment_draft', []);

        return view('app.assessment', compact('questions', 'total', 'step', 'savedAnswers'));
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
        $topCareers    = $this->scoring->getTopCareers($scores['riasec']);
        $topCareerIds  = array_column($topCareers, 'id');

        $result = $this->assessmentRepo->saveResult(
            auth()->id(),
            $scores,
            $topCareerIds
        );

        session(['assessment_result_id' => $result->id]);

        // Clear draft after successful submission
        session()->forget('assessment_draft');

        $this->llm->generate(auth()->id(), 'career_recommendation', [
            'riasec'   => $scores['riasec'],
            'big_five' => $scores['big_five'],
            'top'      => $topCareerIds,
        ]);

        return redirect()->route('assessment.result');
    }

    public function result()
    {
        $result = $this->assessmentRepo->getLatestResult(auth()->id());

        if (! $result) {
            return redirect()->route('assessment');
        }

        $scores     = $result->riasec_scores ?? [];
        $topCareers = $this->scoring->getTopCareers($scores);

        $narrative = $this->llm->generate(auth()->id(), 'career_recommendation', [
            'riasec'   => $scores,
            'big_five' => $result->big_five_scores ?? [],
            'top'      => $result->top_career_ids ?? [],
        ]);

        return view('app.assessment-result', [
            'recommendations' => $topCareers,
            'narrative'       => $narrative,
            'riasec'          => $scores,
        ]);
    }
}
