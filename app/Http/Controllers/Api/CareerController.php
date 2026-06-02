<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Career;

class CareerController extends Controller
{
    public function index()
    {
        $careers = Career::where('is_active', true)
            ->with('skills')
            ->get()
            ->map(fn ($c) => [
                'id'               => $c->id,
                'name'             => $c->name,
                'description'      => $c->description,
                'riasec_code'      => $c->riasec_code,
                'industry_standard'=> $c->industry_standard,
                'skills'           => $c->skills->map(fn ($s) => [
                    'id'   => $s->id,
                    'name' => $s->name,
                    'level'=> $s->level,
                ]),
            ]);

        return response()->json(['careers' => $careers]);
    }
}
