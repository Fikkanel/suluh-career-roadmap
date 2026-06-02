<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RoadmapArchive;

class ArchiveController extends Controller
{
    public function index()
    {
        $archives = RoadmapArchive::where('user_id', auth()->id())
            ->with('career')
            ->latest('archived_at')
            ->get()
            ->map(fn ($a) => [
                'id'               => $a->id,
                'career'           => $a->career_name ?? ($a->career?->name ?? 'Karir tidak diketahui'),
                'archived_at'      => $a->archived_at?->diffForHumans() ?? '—',
                'completed_skills' => $a->completed_skills,
                'total_skills'     => $a->total_skills,
                'reflection'       => $a->reflection,
            ])
            ->toArray();

        return view('app.archive', compact('archives'));
    }
}
