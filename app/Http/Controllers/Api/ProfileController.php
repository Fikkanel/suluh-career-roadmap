<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('currentCareer');

        return response()->json([
            'profile' => [
                'name'              => $user->name,
                'email'             => $user->email,
                'age_range'         => $user->age_range,
                'education_level'   => $user->education_level,
                'work_experience'   => $user->work_experience,
                'current_career'    => $user->currentCareer?->only(['id', 'name', 'riasec_code']),
                'personality_scores'=> $user->personality_scores,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name'            => 'sometimes|string|max:255',
            'age_range'       => 'sometimes|string|max:50',
            'education_level' => 'sometimes|string|max:100',
            'work_experience' => 'sometimes|string|max:100',
        ]);

        $request->user()->update($data);

        return response()->json([
            'message' => 'Profil diperbarui.',
            'profile' => $request->user()->fresh()->only([
                'name', 'email', 'age_range', 'education_level', 'work_experience',
            ]),
        ]);
    }
}
