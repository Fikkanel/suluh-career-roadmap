<?php

namespace App\Http\Controllers;

use App\Models\EthicsDecision;
use Illuminate\Http\Request;

class DataEthicsController extends Controller
{
    public function index()
    {
        // Pastikan ada mock data awal jika tabel kosong (hanya untuk showcase MVP)
        if (EthicsDecision::count() === 0) {
            EthicsDecision::insert([
                [
                    'title' => 'Kerjasama dengan Glints untuk Rekomendasi Loker',
                    'context' => 'Glints meminta akses anonim ke data skill pengguna untuk mencocokkan loker.',
                    'decision' => 'Disetujui dengan syarat data yang dikirim hanya skill yang sudah "done" tanpa nama pengguna.',
                    'status' => 'approved',
                    'votes_for' => 5,
                    'votes_against' => 1,
                    'implementation_date' => '2026-06-01',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'title' => 'Penggunaan AI Pihak Ketiga untuk Analisis Kepribadian',
                    'context' => 'Penggunaan OpenAI API untuk memproses skor kepribadian raw.',
                    'decision' => 'Ditolak karena risiko privasi pada data psikologis raw.',
                    'status' => 'rejected',
                    'votes_for' => 2,
                    'votes_against' => 4,
                    'implementation_date' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'title' => 'Pembaruan Kebijakan Penghapusan Data (Right to be Forgotten)',
                    'context' => 'Berapa lama data arsip (Roadmap Archive) harus disimpan setelah pengguna menghapus akun?',
                    'decision' => null,
                    'status' => 'voting',
                    'votes_for' => 3,
                    'votes_against' => 0,
                    'implementation_date' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }

        $decisions = EthicsDecision::orderBy('created_at', 'desc')->get();

        return view('public.ethics', compact('decisions'));
    }

    public function vote(Request $request, $id)
    {
        $request->validate([
            'vote' => 'required|in:for,against'
        ]);

        $decision = EthicsDecision::findOrFail($id);
        
        if ($decision->status !== 'voting') {
            return back()->withErrors(['Hanya proposal berstatus "voting" yang dapat divoting.']);
        }

        // Cegah user vote lebih dari satu kali per proposal
        $votedKey = 'ethics_voted_' . $id;
        if (session()->has($votedKey)) {
            return back()->withErrors(['Kamu sudah memberikan vote pada proposal ini.']);
        }

        if ($request->vote === 'for') {
            $decision->increment('votes_for');
        } else {
            $decision->increment('votes_against');
        }

        session([$votedKey => true]);

        return back()->with('success', 'Vote berhasil direkam secara transparan.');
    }
}
