<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Models\Career;
use App\Models\Skill;
use App\Models\UserProgress;
use App\Services\RoadmapGeneratorService;
use App\Services\LLMNarrativeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoadmapGeneratorServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_uses_existing_skills_if_present(): void
    {
        $user = User::factory()->create();
        $career = Career::factory()->create(['is_active' => true]);
        
        $skill1 = Skill::create([
            'career_id' => $career->id,
            'name' => 'Skill 1',
            'level' => 'beginner',
            'estimated_hours' => 10,
            'order' => 1,
        ]);

        $mockLLM = $this->mock(LLMNarrativeService::class);
        $mockLLM->shouldNotReceive('generate');

        $service = new RoadmapGeneratorService($mockLLM);
        $stages = $service->generate($user->id, $career->id);

        $this->assertCount(1, $stages);
        $this->assertEquals('Skill 1', $stages[0]['name']);
        $this->assertEquals('beginner', $stages[0]['level']);
        $this->assertEquals('not_started', $stages[0]['status']);
    }

    public function test_generate_calls_llm_service_if_no_skills_present(): void
    {
        $user = User::factory()->create(['major' => 'Teknik Informatika']);
        $career = Career::factory()->create(['is_active' => true]);

        $mockLLM = $this->mock(LLMNarrativeService::class);
        $mockLLM->shouldReceive('generate')
            ->once()
            ->with($user->id, 'roadmap_generation', [
                'career_name' => $career->name,
                'major' => 'Teknik Informatika'
            ])
            ->andReturn(json_encode([
                [
                    'name' => 'AI Skill 1',
                    'level' => 'beginner',
                    'estimated_hours' => 15,
                    'order' => 1,
                    'validation_type' => 'scenario',
                    'scenario_question' => 'How to solve this case?'
                ],
                [
                    'name' => 'AI Skill 2',
                    'level' => 'intermediate',
                    'estimated_hours' => 25,
                    'order' => 2,
                    'validation_type' => 'reflection',
                    'scenario_question' => null
                ]
            ]));

        $service = new RoadmapGeneratorService($mockLLM);
        $stages = $service->generate($user->id, $career->id);

        $this->assertCount(2, $stages);
        $this->assertEquals('AI Skill 1', $stages[0]['name']);
        $this->assertEquals('beginner', $stages[0]['level']);
        
        $this->assertDatabaseHas('skills', [
            'career_id' => $career->id,
            'name' => 'AI Skill 1',
            'validation_type' => 'scenario',
            'scenario_question' => 'How to solve this case?'
        ]);

        $this->assertDatabaseHas('skills', [
            'career_id' => $career->id,
            'name' => 'AI Skill 2',
            'validation_type' => 'reflection',
            'scenario_question' => null
        ]);
    }
}
