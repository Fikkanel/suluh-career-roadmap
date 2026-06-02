<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Eloquent\AssessmentRepository;
use App\Services\ScoringService;
use App\Services\LLMNarrativeService;

class AssessmentController extends Controller
{
    public function __construct(
        private readonly AssessmentRepository  $assessmentRepo,
        private readonly ScoringService       $scoring,
        private readonly LLMNarrativeService  $llm,
    ) {}

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

        $scores       = $this->scoring->calculateScores($answers, $questions);
        $topCareers   = $this->scoring->getTopCareers($scores['riasec']);
        $topCareerIds = array_column($topCareers, 'id');

        $result = $this->assessmentRepo->saveResult(
            $request->user()->id,
            $scores,
            $topCareerIds
        );

        $this->llm->generate($request->user()->id, 'career_recommendation', [
            'riasec'   => $scores['riasec'],
            'big_five' => $scores['big_five'],
            'top'      => $topCareerIds,
        ]);

        return response()->json([
            'message' => 'Asesmen berhasil disubmit.',
            'result'  => [
                'id'          => $result->id,
                'riasec'      => $scores['riasec'],
                'big_five'    => $scores['big_five'],
                'top_careers' => $topCareers,
            ],
        ]);
    }
}
