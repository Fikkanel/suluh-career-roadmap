<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProgress;
use App\Models\MentorFeedback;
use App\Models\RoadmapArchive;
use Illuminate\Http\Request;

class MentorDashboardController extends Controller
{
    public function index()
    {
        $mentor = auth()->user();

        // Get mentees (users who are not admin and not mentors)
        $mentees = User::where('is_admin', false)
            ->where('role', 'user')
            ->whereNotNull('current_career_id')
            ->with(['currentCareer'])
            ->get()
            ->map(function ($mentee) {
                // Calculate progress manually for simplicity
                $total = UserProgress::where('user_id', $mentee->id)->count();
                $done = UserProgress::where('user_id', $mentee->id)->where('status', 'done')->count();
                $mentee->crs_score = $total > 0 ? (int) round($done / $total * 100) : 0;
                return $mentee;
            })
            ->sortByDesc('crs_score');

        return view('mentor.dashboard', compact('mentees', 'mentor'));
    }

    public function showMentee($userId)
    {
        $mentee = User::findOrFail($userId);
        
        // Ensure user is actually a mentee
        if ($mentee->role !== 'user' && !$mentee->is_admin) {
            // allowing to view anyone for now
        }

        $progresses = UserProgress::where('user_id', $userId)
            ->with(['skill.career'])
            ->get();
            
        $feedbacks = MentorFeedback::where('user_id', $userId)
            ->with('mentor')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mentor.mentee-detail', compact('mentee', 'progresses', 'feedbacks'));
    }

    public function storeFeedback(Request $request, $userId)
    {
        $request->validate([
            'content' => 'required|string|min:5|max:2000',
        ]);

        MentorFeedback::create([
            'mentor_id' => auth()->id(),
            'user_id' => $userId,
            'content' => $request->content,
            // roadmap_archive_id can be null if it's a general feedback
        ]);

        return back()->with('success', 'Feedback berhasil dikirimkan kepada mentee.');
    }
}
