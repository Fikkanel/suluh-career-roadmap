<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Skill;
use App\Models\SkillValidation;

class SkillValidationController extends Controller
{
    public function show($skillId)
    {
        $skill = Skill::findOrFail($skillId);
        $user = auth()->user();

        $existingValidation = SkillValidation::where('user_id', $user->id)
            ->where('skill_id', $skillId)
            ->first();

        return view('app.skill-validation', compact('skill', 'existingValidation'));
    }

    public function store(Request $request, $skillId)
    {
        $skill = Skill::findOrFail($skillId);
        $user = auth()->user();

        $data = $request->validate([
            'response'           => 'required|string|max:2000',
            'self_assessed_level' => 'nullable|integer|min:1|max:5',
            'type'               => 'required|in:scenario,reflection,behavior',
        ]);

        SkillValidation::updateOrCreate(
            ['user_id' => $user->id, 'skill_id' => $skillId, 'type' => $data['type']],
            [
                'response'            => $data['response'],
                'self_assessed_level' => $data['self_assessed_level'] ?? null,
                'validated_at'        => now(),
            ]
        );

        return back()->with('success', 'Refleksi skill berhasil disimpan. Terima kasih telah berbagi pengalamanmu!');
    }
}
