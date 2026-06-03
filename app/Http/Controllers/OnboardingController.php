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
            'age_range'             => 'required|string|max:20',
            'education_level'       => 'required|string|max:50',
            'major'                 => 'required|string|max:100',
            'work_experience'       => 'required|string|max:20',
            'province'              => 'required|string|max:100',
            'exploration_readiness' => 'required|string|max:20',
            'support_level'         => 'required|string|max:20',
        ]);

        $user = auth()->user();
        $user->update($data);

        // Hapus cache narasi LLM karena profil/jurusan telah diperbarui
        \App\Models\LlmNarrativeCache::where('user_id', $user->id)->delete();

        return redirect()->route('assessment');
    }
}
