<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AssessmentResult;

class EnsureAssessmentCompleted
{
    /**
     * Ensure the user has completed the career assessment before accessing
     * protected pages like Dashboard, Roadmap, Skill Progress, etc.
     *
     * If the user has assessment results but hasn't chosen a career yet,
     * they are sent to the results page. If they haven't taken the assessment
     * at all, they are sent to the questionnaire.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Only enforce for regular student users
        if ($user && !$user->is_admin && !in_array($user->role, ['mentor', 'institution'])) {
            // Check if user has a chosen career — if yes, they're good to go
            if (!$user->current_career_id) {
                // Check if they at least have assessment results
                $hasResult = AssessmentResult::where('user_id', $user->id)->exists();

                if ($hasResult) {
                    // Izinkan akses detail karir dan pemilihan karir agar pengguna bisa melihat/memilih karir
                    if ($request->routeIs('career.detail') || $request->routeIs('career.choose')) {
                        return $next($request);
                    }

                    // They completed assessment but haven't chosen a career yet
                    return redirect()->route('assessment.result')
                        ->with('info', 'Pilih karir dari hasil asesmen untuk melanjutkan.');
                }

                // They haven't taken the assessment at all
                return redirect()->route('assessment')
                    ->with('info', 'Lengkapi asesmen karir terlebih dahulu untuk membuka fitur ini.');
            }
        }

        return $next($request);
    }
}
