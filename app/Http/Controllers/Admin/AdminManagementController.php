<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Career;
use App\Models\AssessmentQuestion;
use App\Models\EthicsDecision;
use App\Models\User;

class AdminManagementController extends Controller
{
    /* ── Careers Management ─────────────────────────────────────── */

    public function careersIndex()
    {
        $careers = Career::withCount('skills')->orderBy('name')->get()->map(fn ($c) => [
            'id'                => $c->id,
            'name'              => $c->name,
            'riasec_code'       => $c->riasec_code,
            'skills_count'      => $c->skills_count,
            'is_active'         => $c->is_active,
            'description'       => $c->description,
            'industry_standard' => $c->industry_standard,
        ])->toArray();

        return view('admin.careers', compact('careers'));
    }

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

    /* ── Questions Management ───────────────────────────────────── */

    public function questionsIndex()
    {
        $questions = AssessmentQuestion::where('is_active', true)->orderBy('order')->get()->map(fn ($q) => [
            'id'              => $q->id,
            'prompt'          => $q->prompt,
            'riasec_category' => $q->riasec_category,
            'big_five_trait'  => $q->big_five_trait,
            'weight'          => $q->weight,
            'type'            => $q->type,
            'order'           => $q->order,
        ])->toArray();

        return view('admin.questions', compact('questions'));
    }

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

    /* ── Ethics Decisions Management ────────────────────────────── */

    public function ethicsIndex()
    {
        $ethicsDecisions = EthicsDecision::orderBy('created_at', 'desc')->get();

        return view('admin.ethics', compact('ethicsDecisions'));
    }

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

    /* ── User & Mentor Moderation ───────────────────────────────── */

    public function usersIndex()
    {
        $users = User::orderBy('name')->get()->map(fn ($u) => [
            'id'              => $u->id,
            'name'            => $u->name,
            'email'           => $u->email,
            'role'            => $u->role ?? 'user',
            'is_admin'        => $u->is_admin,
            'education_level' => $u->education_level,
            'major'           => $u->major,
        ])->toArray();

        return view('admin.users', compact('users'));
    }

    public function updateUser(Request $request, User $user)
    {
        if ($user->is_admin) {
            return back()->with('error', 'Anda tidak dapat mengubah peran atau status pengguna administrator.');
        }

        $data = $request->validate([
            'role'     => 'required|in:user,mentor,institution',
            'is_admin' => 'required|boolean',
        ]);

        $user->update($data);

        return back()->with('success', 'Peran pengguna ' . $user->name . ' berhasil diperbarui.');
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role'     => 'required|in:user,mentor,institution',
            'is_admin' => 'required|boolean',
        ]);

        User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'], // Eloquent hashes this automatically via the 'hashed' cast
            'role'     => $data['role'],
            'is_admin' => $data['is_admin'],
        ]);

        return back()->with([
            'success'           => 'Akun pengguna baru berhasil dibuat!',
            'new_user_email'    => $data['email'],
            'new_user_password' => $data['password'],
        ]);
    }

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->is_admin) {
            return back()->with('error', 'Anda tidak dapat menghapus akun administrator.');
        }

        $user->delete();
        return back()->with('success', 'Akun pengguna berhasil dihapus.');
    }
}
