<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingCompleted
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Enforce onboarding only for regular/student users
        if ($user && !$user->is_admin && !in_array($user->role, ['mentor', 'institution'])) {
            $requiredFields = [
                'age_range',
                'education_level',
                'major',
                'university_name',
                'work_experience',
                'province',
                'exploration_readiness',
                'support_level',
            ];

            $incomplete = false;
            foreach ($requiredFields as $field) {
                if (empty($user->$field)) {
                    $incomplete = true;
                    break;
                }
            }

            if ($incomplete && !$request->routeIs('onboarding') && !$request->routeIs('onboarding.store') && !$request->routeIs('logout')) {
                return redirect()->route('onboarding')
                    ->with('info', 'Silakan lengkapi data profil Anda terlebih dahulu.');
            }
        }

        return $next($request);
    }
}
