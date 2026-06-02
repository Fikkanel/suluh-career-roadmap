<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\AssessmentResult;
use App\Models\ContextScore;
use App\Models\ImpactSurvey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EncryptionPseudonymTest extends TestCase
{
    use RefreshDatabase;

    public function test_assessment_result_auto_generates_pseudonym_id(): void
    {
        $user = User::factory()->create();
        $result = AssessmentResult::create([
            'user_id'        => $user->id,
            'riasec_scores'  => ['R' => 0.5, 'I' => 0.8],
            'big_five_scores' => ['Openness' => 0.7],
        ]);

        $this->assertNotEmpty($result->pseudonym_id);
        $this->assertEquals(32, strlen($result->pseudonym_id));
        $this->assertMatchesRegularExpression('/^[0-9a-f]{32}$/', $result->pseudonym_id);
    }

    public function test_context_score_auto_generates_pseudonym_id(): void
    {
        $user = User::factory()->create();
        $cs = ContextScore::create([
            'user_id' => $user->id,
            'score'   => 50,
            'level'   => 'medium',
        ]);

        $this->assertNotEmpty($cs->pseudonym_id);
        $this->assertEquals(32, strlen($cs->pseudonym_id));
    }

    public function test_impact_survey_auto_generates_pseudonym_id(): void
    {
        $user = User::factory()->create();
        $survey = ImpactSurvey::create([
            'user_id'    => $user->id,
            'type'       => '3_months',
            'crs_before' => 30,
        ]);

        $this->assertNotEmpty($survey->pseudonym_id);
        $this->assertEquals(32, strlen($survey->pseudonym_id));
    }

    public function test_riasec_scores_stored_encrypted(): void
    {
        $user = User::factory()->create();
        $scores = ['R' => 0.5, 'I' => 0.8, 'A' => 0.3, 'S' => 0.6, 'E' => 0.4, 'C' => 0.7];

        $result = AssessmentResult::create([
            'user_id'        => $user->id,
            'riasec_scores'  => $scores,
            'big_five_scores' => ['Openness' => 0.7],
        ]);

        // Retrieve raw from DB without model hydration
        $raw = \DB::table('assessment_results')->where('id', $result->id)->value('riasec_scores');

        // Raw value should NOT be plain JSON (it's encrypted)
        $this->assertNotEquals(json_encode($scores), $raw);

        // Model accessor should return decrypted array
        $this->assertEquals($scores, $result->riasec_scores);
    }

    public function test_big_five_scores_stored_encrypted(): void
    {
        $user = User::factory()->create();
        $scores = ['Openness' => 0.7, 'Conscientiousness' => 0.6, 'Extraversion' => 0.5, 'Agreeableness' => 0.8, 'Neuroticism' => 0.3];

        $result = AssessmentResult::create([
            'user_id'         => $user->id,
            'riasec_scores'   => ['R' => 0.5],
            'big_five_scores' => $scores,
        ]);

        $raw = \DB::table('assessment_results')->where('id', $result->id)->value('big_five_scores');
        $this->assertNotEquals(json_encode($scores), $raw);
        $this->assertEquals($scores, $result->big_five_scores);
    }

    public function test_personality_scores_stored_encrypted(): void
    {
        $scores = ['R' => 0.5, 'I' => 0.8];
        $user = User::factory()->create(['personality_scores' => $scores]);

        $raw = \DB::table('users')->where('id', $user->id)->value('personality_scores');
        $this->assertNotEquals(json_encode($scores), $raw);
        $this->assertEquals($scores, $user->personality_scores);
    }

    public function test_context_score_factors_stored_encrypted(): void
    {
        $user = User::factory()->create();
        $factors = ['work_experience' => '1-2y', 'login_count' => 3];

        $cs = ContextScore::create([
            'user_id' => $user->id,
            'score'   => 50,
            'level'   => 'medium',
            'factors' => $factors,
        ]);

        $raw = \DB::table('context_scores')->where('id', $cs->id)->value('factors');
        $this->assertNotEquals(json_encode($factors), $raw);
        $this->assertEquals($factors, $cs->factors);
    }

    public function test_impact_survey_answers_stored_encrypted(): void
    {
        $user = User::factory()->create();
        $answers = ['employed' => 'yes', 'satisfaction' => 4];

        $survey = ImpactSurvey::create([
            'user_id'    => $user->id,
            'type'       => '3_months',
            'answers'    => $answers,
        ]);

        $raw = \DB::table('impact_surveys')->where('id', $survey->id)->value('answers');
        $this->assertNotEquals(json_encode($answers), $raw);
        $this->assertEquals($answers, $survey->answers);
    }

    public function test_pseudonym_ids_are_unique(): void
    {
        $user = User::factory()->create();
        $ids = [];

        for ($i = 0; $i < 10; $i++) {
            $result = AssessmentResult::create([
                'user_id'        => $user->id,
                'riasec_scores'  => ['R' => 0.5],
                'big_five_scores' => [],
            ]);
            $ids[] = $result->pseudonym_id;
        }

        $this->assertEquals(10, count(array_unique($ids)));
    }
}
