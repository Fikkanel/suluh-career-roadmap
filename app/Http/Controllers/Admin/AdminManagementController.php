<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Career;
use App\Models\AssessmentQuestion;
use App\Models\EthicsDecision;

class AdminManagementController extends Controller
{
    public function index()
    {
        $careers = Career::withCount('skills')->orderBy('name')->get()->map(fn ($c) => [
            'id'          => $c->id,
            'name'        => $c->name,
            'riasec_code' => $c->riasec_code,
            'skills_count'=> $c->skills_count,
            'is_active'   => $c->is_active,
        ])->toArray();

        $questions = AssessmentQuestion::where('is_active', true)->orderBy('order')->get()->map(fn ($q) => [
            'id'              => $q->id,
            'prompt'          => $q->prompt,
            'riasec_category' => $q->riasec_category,
            'big_five_trait'  => $q->big_five_trait,
            'weight'          => $q->weight,
            'type'            => $q->type,
            'order'           => $q->order,
        ])->toArray();

        $ethicsDecisions = EthicsDecision::orderBy('created_at', 'desc')->get();

        return view('admin.management', compact('careers', 'questions', 'ethicsDecisions'));
    }

    /* ── Career CRUD ──────────────────────────────────────────── */

    public function storeCareer(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'required|string',
            'riasec_code'       => 'required|string|max:6',
            'industry_standard' => 'required|string|max:100',
        ]);

        Career::create(array_merge($data, ['is_active' => true, 'slug' => Str::slug($data['name'])]));

        return back()->with('success', 'Karir berhasil ditambahkan.');
    }

    public function updateCareer(Request $request, Career $career)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'required|string',
            'riasec_code'       => 'required|string|max:6',
            'industry_standard' => 'required|string|max:100',
            'is_active'         => 'boolean',
        ]);

        $career->update($data);

        return back()->with('success', 'Karir berhasil diperbarui.');
    }

    public function destroyCareer(Career $career)
    {
        $career->update(['is_active' => false]);
        return back()->with('success', 'Karir dinonaktifkan.');
    }

    /* ── Question CRUD ───────────────────────────────────────── */

    public function storeQuestion(Request $request)
    {
        $data = $request->validate([
            'prompt'          => 'required|string|max:1000',
            'type'            => 'required|in:single_choice,scale,text_reflection',
            'riasec_category' => 'required|in:R,I,A,S,E,C',
            'big_five_trait'  => 'required|in:Openness,Conscientiousness,Extraversion,Agreeableness,Neuroticism',
            'weight'          => 'required|numeric|min:0.1|max:3',
            'order'           => 'required|integer|min:1',
            'options'         => 'nullable|json',
        ]);

        AssessmentQuestion::create(array_merge($data, [
            'is_active' => true,
            'options'   => $data['options'] ? json_decode($data['options'], true) : null,
        ]));

        return back()->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    public function updateQuestion(Request $request, AssessmentQuestion $question)
    {
        $data = $request->validate([
            'prompt'          => 'required|string|max:1000',
            'type'            => 'required|in:single_choice,scale,text_reflection',
            'riasec_category' => 'required|in:R,I,A,S,E,C',
            'big_five_trait'  => 'required|in:Openness,Conscientiousness,Extraversion,Agreeableness,Neuroticism',
            'weight'          => 'required|numeric|min:0.1|max:3',
            'order'           => 'required|integer|min:1',
            'is_active'       => 'boolean',
        ]);

        $question->update($data);

        return back()->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    public function destroyQuestion(AssessmentQuestion $question)
    {
        $question->update(['is_active' => false]);
        return back()->with('success', 'Pertanyaan dinonaktifkan.');
    }

    /* ── Ethics Decision CRUD ───────────────────────────────────── */

    public function storeEthics(Request $request)
    {
        $data = $request->validate([
            'title'   => 'required|string|max:255',
            'context' => 'required|string',
            'status'  => 'required|in:voting,approved,rejected',
            'decision'=> 'nullable|string',
            'implementation_date' => 'nullable|date',
        ]);

        EthicsDecision::create(array_merge($data, [
            'votes_for'     => 0,
            'votes_against' => 0,
        ]));

        return back()->with('success', 'Proposal Komite Etika berhasil ditambahkan.');
    }

    public function destroyEthics(EthicsDecision $ethics)
    {
        $ethics->delete();
        return back()->with('success', 'Proposal berhasil dihapus.');
    }
}
