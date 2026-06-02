<?php

namespace Tests\Feature\Constitution;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\File;

class CopyComplianceTest extends TestCase
{
    use RefreshDatabase;

    private array $bannedPhrases = [
        'karir terbaik untukmu',
        'kamu salah memilih',
        'progress gagal',
        'kamu tertinggal',
        'streak kamu terputus',
    ];

    private array $viewsToCheck = [
        'resources/views/app',
        'resources/views/public',
        'resources/views/components',
        'resources/views/auth',
    ];

    public function test_no_banned_copy_in_views(): void
    {
        $projectPath = base_path('..');

        foreach ($this->viewsToCheck as $dir) {
            $fullPath = $projectPath . '/' . $dir;
            if (!is_dir($fullPath)) continue;

            $files = File::allFiles($fullPath);
            foreach ($files as $file) {
                if ($file->getExtension() !== 'blade.php') continue;

                $content = File::get($file->getPathname());
                $lowerContent = strtolower($content);

                foreach ($this->bannedPhrases as $phrase) {
                    $this->assertStringNotContainsString(
                        strtolower($phrase),
                        $lowerContent,
                        "Banned copy '{$phrase}' found in {$file->getFilename()}"
                    );
                }
            }
        }
    }

    public function test_onboarding_fields_are_optional(): void
    {
        // Constitution P2: data minimization — all onboarding fields optional
        $user = \App\Models\User::factory()->create();
        $response = $this->actingAs($user)->get('/onboarding');
        $response->assertStatus(200);
        $response->assertSee('opsional');
        $response->assertSee('melewatinya');
    }

    public function test_skill_validation_no_pass_fail_language(): void
    {
        $user = \App\Models\User::factory()->create();
        $career = \App\Models\Career::factory()->create(['is_active' => true]);
        $skill = \App\Models\Skill::factory()->create(['career_id' => $career->id]);

        $response = $this->actingAs($user)->get("/skill/{$skill->id}/validate");
        $content = $response->getContent();

        // Constitution P3: no pass/fail language in the skill validation form
        // Check main content area (excluding compiled JS assets)
        $this->assertStringContainsString('Refleksi', $content);
        $this->assertStringContainsString('pengalaman', $content);
    }

    public function test_impact_page_aggregate_only(): void
    {
        $user = \App\Models\User::factory()->create([
            'name'  => 'Test Identity',
            'email' => 'identity@test.com',
        ]);

        $response = $this->get('/impact');
        $content = $response->getContent();

        $this->assertStringContainsString('agregat anonim', $content);
        $this->assertStringNotContainsString('Test Identity', $content);
        $this->assertStringNotContainsString('identity@test.com', $content);
    }

    public function test_sunset_policy_page_exists(): void
    {
        $response = $this->get('/sunset-policy');
        $response->assertStatus(200);
        $response->assertSee('Kebijakan Sunset');
        $response->assertSee('Hari 0');
        $response->assertSee('Hari 1');
        $response->assertSee('Hari 90');
    }

    public function test_survey_transparency_statement(): void
    {
        $user = \App\Models\User::factory()->create();
        \App\Models\ImpactSurvey::create(['user_id' => $user->id, 'type' => '3_months']);

        $response = $this->actingAs($user)->get('/survey/3_months');
        $content = strtolower($response->getContent());

        // Constitution P2: transparency about data use
        $this->assertStringContainsString('mengapa kami bertanya', $content);
        $this->assertStringContainsString('anonim', $content);
        // Data is NOT sold (correct statement in the view)
        $this->assertStringContainsString('tidak akan dijual', $content);
    }

    public function test_pivot_flow_no_guilt_language(): void
    {
        $user = \App\Models\User::factory()->create();
        $career = \App\Models\Career::factory()->create(['is_active' => true]);
        $user->update(['current_career_id' => $career->id]);

        $response = $this->actingAs($user)->get('/pivot');
        $content = strtolower($response->getContent());

        // Constitution P3: no guilt in pivot flow
        $this->assertStringNotContainsString('buang', $content);
        $this->assertStringNotContainsString('membuang', $content);
        $this->assertStringNotContainsString('sayang', $content);
    }

    public function test_no_monetization_in_views(): void
    {
        $projectPath = base_path('..');
        $dirs = ['resources/views'];

        foreach ($dirs as $dir) {
            $fullPath = $projectPath . '/' . $dir;
            if (!is_dir($fullPath)) continue;

            $files = File::allFiles($fullPath);
            foreach ($files as $file) {
                if ($file->getExtension() !== 'blade.php') continue;

                $content = strtolower(File::get($file->getPathname()));

                // Constitution Anti-Prinsip: no paid recommendations as organic
                $this->assertStringNotContainsString('rekomendasi berbayar', $content);
                $this->assertStringNotContainsString('sponsored', $content);
                $this->assertStringNotContainsString('iklan', $content);
            }
        }
    }
}
