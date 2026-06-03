<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LLMNarrativeService;

class ChatbotController extends Controller
{
    public function __construct(
        private readonly LLMNarrativeService $llmService
    ) {}

    public function message(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'nullable|array',
        ]);

        $user = auth()->user();
        $message = $request->input('message');
        $history = $request->input('history', []);

        // Format history for the LLM
        $historyText = '';
        foreach (array_slice($history, -6) as $chat) {
            $sender = ($chat['sender'] === 'user') ? 'Pengguna' : 'Asisten (Suluh Career Bot)';
            $historyText .= "{$sender}: {$chat['text']}\n";
        }

        // Generate response using LLM
        $response = $this->llmService->generate($user->id, 'chatbot_response', [
            'major'        => $user->major ?? 'Umum',
            'career'       => $user->currentCareer?->name ?? 'Belum memilih',
            'history_text' => $historyText,
            'message'      => $message,
        ]);

        return response()->json([
            'reply' => $response
        ]);
    }
}
