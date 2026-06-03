<?php

namespace Tests\Feature\Web;

use App\Models\AssessmentQuestion;
use App\Models\Career;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed some careers and active questions
        Career::factory()->create(['riasec_code' => 'IRA', 'is_active' => true]);

        AssessmentQuestion::create([
            'prompt' => 'Question 1 scale',
            'type' => 'scale',
            'riasec_category' => 'I',
            'big_five_trait' => 'Openness',
            'weight' => 1.0,
            'is_active' => true,
            'order' => 1,
        ]);

        AssessmentQuestion::create([
            'prompt' => 'Question 2 choice',
            'type' => 'single_choice',
            'riasec_category' => 'R',
            'big_five_trait' => 'Conscientiousness',
            'weight' => 1.0,
            'is_active' => true,
            'options' => ['a' => 'Option A', 'b' => 'Option B'],
            'order' => 2,
        ]);

        AssessmentQuestion::create([
            'prompt' => 'Question 3 text',
            'type' => 'text_reflection',
            'riasec_category' => 'A',
            'big_five_trait' => 'Agreeableness',
            'weight' => 1.0,
            'is_active' => true,
            'order' => 3,
        ]);
    }

    public function test_assessment_page_requires_auth(): void
    {
        $response = $this->get(route('assessment'));
        $response->assertRedirect(route('login'));
    }

    public function test_can_view_assessment_page(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('assessment'));

        $response->assertStatus(200);
        $response->assertSee('Question 1 scale');
        $response->assertSee('Question 2 choice');
        $response->assertSee('Question 3 text');
    }

    public function test_can_save_draft_and_retrieves_correctly(): void
    {
        $user = User::factory()->create();
        $questions = AssessmentQuestion::all();

        $q1 = $questions[0];
        $q2 = $questions[1];
        $q3 = $questions[2];

        $response = $this->actingAs($user)->post(route('assessment.saveDraft'), [
            'answers' => [
                $q1->id => 8,
                $q2->id => 'a',
                $q3->id => 'My reflection text',
            ]
        ]);

        $response->assertRedirect(route('assessment'));
        $response->assertSessionHas('success');
        $this->assertEquals([
            $q1->id => 8,
            $q2->id => 'a',
            $q3->id => 'My reflection text',
        ], session('assessment_draft'));

        // Check if pre-filled on page reload
        $responseReload = $this->actingAs($user)->get(route('assessment'));
        $responseReload->assertStatus(200);
        $responseReload->assertSee('checked'); // Radio options should be checked
        $responseReload->assertSee('My reflection text');
    }

    public function test_can_submit_assessment_successfully(): void
    {
        $user = User::factory()->create();
        $questions = AssessmentQuestion::all();

        $q1 = $questions[0];
        $q2 = $questions[1];
        $q3 = $questions[2];

        $response = $this->actingAs($user)->post(route('assessment.submit'), [
            'answers' => [
                $q1->id => 5,
                $q2->id => 'b',
                $q3->id => 'Another text',
            ]
        ]);

        $response->assertRedirect(route('assessment.result'));
        $this->assertDatabaseHas('assessment_results', [
            'user_id' => $user->id,
        ]);
        $this->assertNull(session('assessment_draft'));
    }

    public function test_can_view_assessment_result_page(): void
    {
        $user = User::factory()->create(['major' => 'Pendidikan Guru Sekolah Dasar']);
        
        $result = \App\Models\AssessmentResult::create([
            'user_id' => $user->id,
            'riasec_scores' => ['R' => 50, 'I' => 30, 'A' => 40, 'S' => 80, 'E' => 60, 'C' => 50],
            'big_five_scores' => ['Openness' => 70, 'Conscientiousness' => 80, 'Extraversion' => 60, 'Agreeableness' => 75, 'Neuroticism' => 40],
            'top_career_ids' => [1],
        ]);

        $this->mock(\App\Services\LLMNarrativeService::class, function ($mock) {
            $mock->shouldReceive('generate')
                ->andReturn(json_encode([
                    'narrative' => 'Arah ini muncul berdasarkan pola jawabanmu. Kamu tetap memegang keputusan akhirnya.',
                    'careers' => [
                        [
                            'name' => 'Konsultan Pendidikan',
                            'description' => 'Membantu lembaga pendidikan dalam meningkatkan kualitas kurikulum dan metode pengajaran.',
                            'riasec_code' => 'SAE',
                            'industry_standard' => 'Pendidikan',
                            'match_percent' => 90,
                            'reasons' => [
                                'Menyelaraskan kemampuan mengajar dengan rancangan program belajar.',
                                'Sesuai dengan skor minat Sosial yang tinggi.'
                            ],
                            'cautions' => [],
                            'is_major_match' => true,
                            'skills' => [
                                ['name' => 'Manajemen Kelas & Kondusivitas', 'level' => 'beginner', 'estimated_hours' => 20, 'order' => 1]
                            ]
                        ]
                    ]
                ]));
        });

        $response = $this->actingAs($user)->get(route('assessment.result'));

        $response->assertStatus(200);
        $response->assertSee('Rekomendasi Karir Terkunci Untukmu');
        $response->assertSee('Konsultan Pendidikan');
    }
}
