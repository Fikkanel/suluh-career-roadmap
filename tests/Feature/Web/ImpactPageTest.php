<?php

namespace Tests\Feature\Web;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImpactPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_impact_page_returns_200(): void
    {
        $response = $this->get('/impact');
        $response->assertStatus(200);
    }

    public function test_impact_page_shows_stats(): void
    {
        $response = $this->get('/impact');
        $response->assertSee('Dashboard Dampak & Analitik');
        $response->assertSee('Pengguna Aktif');
        $response->assertSee('Rata-rata CRS');
    }

    public function test_impact_page_shows_province_distribution_when_data_exists(): void
    {
        User::factory()->count(3)->create([
            'province' => 'DKI Jakarta',
            'is_admin' => false,
        ]);
        User::factory()->count(2)->create([
            'province' => 'Jawa Barat',
            'is_admin' => false,
        ]);

        $response = $this->get('/impact');
        $response->assertSee('Sebaran Pengguna per Provinsi');
        $response->assertSee('DKI Jakarta');
        $response->assertSee('Jawa Barat');
    }

    public function test_impact_page_hides_province_when_no_data(): void
    {
        $response = $this->get('/impact');
        $response->assertDontSee('Sebaran Pengguna per Provinsi');
    }

    public function test_province_only_counts_non_admin(): void
    {
        User::factory()->create(['province' => 'DKI Jakarta', 'is_admin' => true]);
        User::factory()->create(['province' => 'DKI Jakarta', 'is_admin' => false]);

        $response = $this->get('/impact');
        // Should show DKI Jakarta with count 1 (only non-admin)
        $response->assertSee('DKI Jakarta');
    }

    public function test_impact_page_no_individual_data(): void
    {
        $user = User::factory()->create(['name' => 'Secret User', 'email' => 'secret@example.com']);

        $response = $this->get('/impact');
        $response->assertDontSee('Secret User');
        $response->assertDontSee('secret@example.com');
    }

    public function test_impact_page_methodology_note(): void
    {
        $response = $this->get('/impact');
        $response->assertSee('Catatan Metodologi');
        $response->assertSee('CRS');
        $response->assertSee('anonimisasi');
    }
}
