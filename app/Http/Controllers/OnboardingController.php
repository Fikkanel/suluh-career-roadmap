<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function show()
    {
        return view('app.onboarding');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'age_range'             => 'nullable|string|max:20',
            'education_level'       => 'nullable|string|max:50',
            'work_experience'       => 'nullable|string|max:20',
            'province'              => 'nullable|string|max:100',
            'exploration_readiness' => 'nullable|string|max:20',
            'support_level'         => 'nullable|string|max:20',
        ]);

        auth()->user()->update(array_filter($data, fn ($v) => $v !== null));

        return redirect()->route('assessment');
    }
}
