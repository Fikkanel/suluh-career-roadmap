<?php

namespace Tests\Feature\Web;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminAndInstitutionTest extends TestCase
{
    use RefreshDatabase;

    public function test_institution_user_can_generate_and_revoke_api_key(): void
    {
        $institution = User::factory()->create([
            'role' => 'institution',
            'api_key' => null,
        ]);

        $response = $this->actingAs($institution)
            ->post(route('institution.api-key.generate'));

        $response->assertRedirect(route('institution.dashboard'));
        $institution->refresh();
        $this->assertNotNull($institution->api_key);
        $this->assertStringStartsWith('slh_inst_', $institution->api_key);

        $responseRevoke = $this->actingAs($institution)
            ->post(route('institution.api-key.revoke'));

        $responseRevoke->assertRedirect(route('institution.dashboard'));
        $institution->refresh();
        $this->assertNull($institution->api_key);
    }

    public function test_admin_user_can_update_user_roles_and_delete_users(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'role' => 'admin',
        ]);

        $user = User::factory()->create([
            'role' => 'user',
            'is_admin' => false,
        ]);

        $responseUpdate = $this->actingAs($admin)
            ->put(route('admin.users.update', $user->id), [
                'role' => 'mentor',
                'is_admin' => 1,
            ]);

        $responseUpdate->assertRedirect();
        $user->refresh();
        $this->assertEquals('mentor', $user->role);
        $this->assertTrue($user->is_admin);

        $userToDelete = User::factory()->create([
            'role' => 'user',
            'is_admin' => false,
        ]);

        $responseDelete = $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $userToDelete->id));

        $responseDelete->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
    }
}
