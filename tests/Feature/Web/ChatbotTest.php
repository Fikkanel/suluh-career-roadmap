<?php

namespace Tests\Feature\Web;

use App\Models\User;
use App\Models\Career;
use App\Services\LLMNarrativeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatbotTest extends TestCase
{
    use RefreshDatabase;

    public function test_chatbot_requires_authentication(): void
    {
        $response = $this->postJson(route('chatbot.message'), [
            'message' => 'Halo bot'
        ]);

        $response->assertStatus(401);
    }

    public function test_chatbot_validation_error_for_empty_message(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('chatbot.message'), [
            'message' => ''
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['message']);
    }

    public function test_chatbot_returns_ai_response_successfully(): void
    {
        $user = User::factory()->create();

        $this->mock(LLMNarrativeService::class, function ($mock) use ($user) {
            $mock->shouldReceive('generate')
                ->once()
                ->with($user->id, 'chatbot_response', \Mockery::on(function ($context) {
                    return $context['message'] === 'Halo, bagaimana masa depan guru?';
                }))
                ->andReturn('Masa depan guru sangat cerah dengan integrasi teknologi.');
        });

        $response = $this->actingAs($user)->postJson(route('chatbot.message'), [
            'message' => 'Halo, bagaimana masa depan guru?',
            'history' => [
                ['sender' => 'user', 'text' => 'Halo'],
                ['sender' => 'bot', 'text' => 'Halo juga!']
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'reply' => 'Masa depan guru sangat cerah dengan integrasi teknologi.'
        ]);
    }
}
