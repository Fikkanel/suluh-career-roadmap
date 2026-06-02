<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ImpactSurvey;
use App\Repositories\Eloquent\ImpactRepository;
use App\Repositories\Eloquent\ProgressRepository;

class SurveyController extends Controller
{
    public function __construct(
        private readonly ImpactRepository $impactRepo,
        private readonly ProgressRepository $progressRepo,
    ) {}

    public function show($type)
    {
        $user = auth()->user();

        if (!in_array($type, ['3_months', '6_months'])) {
            abort(404);
        }

        $survey = ImpactSurvey::where('user_id', $user->id)
            ->where('type', $type)
            ->first();

        if (!$survey) {
            return redirect()->route('dashboard')
                ->with('info', 'Belum ada survei yang tersedia untukmu saat ini.');
        }

        return view('app.survey', compact('survey', 'type'));
    }

    public function store(Request $request, $type)
    {
        $user = auth()->user();

        if (!in_array($type, ['3_months', '6_months'])) {
            abort(404);
        }

        $data = $request->validate([
            'employed'           => 'required|in:yes,no,looking',
            'career_change'      => 'required|in:yes,no',
            'position_change'    => 'required|in:yes,no',
            'satisfaction'       => 'required|integer|min:1|max:5',
            'feedback'           => 'nullable|string|max:2000',
        ]);

        $survey = ImpactSurvey::where('user_id', $user->id)
            ->where('type', $type)
            ->first();

        if ($survey) {
            $crsAfter = $this->calculateCrs($user->id);

            $survey->update([
                'answers'      => $data,
                'crs_after'    => $crsAfter,
                'submitted_at' => now(),
            ]);
        }

        return redirect()->route('dashboard')
            ->with('success', 'Terima kasih! Survei dampakmu telah tercatat. Kontribusimu membantu kami memahami dampak nyata platform.');
    }

    private function calculateCrs(int $userId): int
    {
        $user = \App\Models\User::find($userId);
        if (!$user || !$user->current_career_id) return 0;

        $total = \App\Models\UserProgress::where('user_id', $userId)->count();
        if ($total === 0) return 0;

        $done = \App\Models\UserProgress::where('user_id', $userId)->where('status', 'done')->count();

        return (int) round($done / $total * 100);
    }
}
