<?php

namespace Tests\Feature\Web;

use Tests\TestCase;

class PreventWebAccessFromApiDomainTest extends TestCase
{
    public function test_web_route_accessed_via_api_domain_redirects_to_main_domain(): void
    {
        // Panggil menggunakan full URL untuk domain api.suluhkarir.my.id
        $response = $this->get('https://api.suluhkarir.my.id/');

        $response->assertRedirect('https://suluhkarir.my.id/');
    }

    public function test_web_route_accessed_via_main_domain_does_not_redirect(): void
    {
        // Panggil menggunakan full URL untuk domain utama
        $response = $this->get('https://suluhkarir.my.id/');

        // Landing page mengembalikan status 200 normal
        $response->assertStatus(200);
    }

    public function test_api_route_accessed_via_api_domain_does_not_redirect(): void
    {
        // Panggil API route menggunakan domain api.suluhkarir.my.id
        $response = $this->postJson('https://api.suluhkarir.my.id/api/v1/auth/register', []);

        // Harus melewati middleware dan memicu validasi form (status 422)
        $response->assertStatus(422);
    }
}
