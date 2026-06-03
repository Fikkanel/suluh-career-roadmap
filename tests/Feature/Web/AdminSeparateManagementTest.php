<?php

namespace Tests\Feature\Web;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminSeparateManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_any_admin_pages(): void
    {
        $this->get(route('admin.careers.index'))->assertRedirect(route('login'));
        $this->get(route('admin.questions.index'))->assertRedirect(route('login'));
        $this->get(route('admin.ethics.index'))->assertRedirect(route('login'));
        $this->get(route('admin.users.index'))->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_access_any_admin_pages(): void
    {
        $user = User::factory()->create([
            'is_admin' => false,
            'role' => 'user',
        ]);

        $this->actingAs($user)->get(route('admin.careers.index'))->assertStatus(403);
        $this->actingAs($user)->get(route('admin.questions.index'))->assertStatus(403);
        $this->actingAs($user)->get(route('admin.ethics.index'))->assertStatus(403);
        $this->actingAs($user)->get(route('admin.users.index'))->assertStatus(403);
    }

    public function test_admin_can_access_careers_page(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.careers.index'));
        $response->assertStatus(200);
        $response->assertSee('Kelola Karir');
        $response->assertSee('Tambah Karir');
    }

    public function test_admin_can_access_questions_page(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.questions.index'));
        $response->assertStatus(200);
        $response->assertSee('Kelola Asesmen');
        $response->assertSee('Pertanyaan Asesmen');
    }

    public function test_admin_can_access_ethics_page(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.ethics.index'));
        $response->assertStatus(200);
        $response->assertSee('Komite Etika');
        $response->assertSee('Komite Etika Data');
    }

    public function test_admin_can_access_users_page(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.users.index'));
        $response->assertStatus(200);
        $response->assertSee('Kelola Pengguna');
        $response->assertSee('Manajemen Peran Pengguna');
    }

    public function test_admin_can_generate_and_store_new_user(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Fikkan Nugroho',
            'email' => 'fikkan@suluh.id',
            'password' => 'SuluhSecurePass123',
            'role' => 'mentor',
            'is_admin' => '0',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('new_user_email', 'fikkan@suluh.id');
        $response->assertSessionHas('new_user_password', 'SuluhSecurePass123');

        $this->assertDatabaseHas('users', [
            'name' => 'Fikkan Nugroho',
            'email' => 'fikkan@suluh.id',
            'role' => 'mentor',
            'is_admin' => false,
        ]);
    }

    public function test_non_admin_cannot_store_new_user(): void
    {
        $user = User::factory()->create([
            'is_admin' => false,
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->post(route('admin.users.store'), [
            'name' => 'Fake User',
            'email' => 'fake@suluh.id',
            'password' => 'somePassword123',
            'role' => 'user',
            'is_admin' => '0',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('users', ['email' => 'fake@suluh.id']);
    }

    public function test_store_user_fails_validation_for_short_password(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Short Pass User',
            'email' => 'shortpass@suluh.id',
            'password' => 'short', // 5 chars, minimum 8
            'role' => 'user',
            'is_admin' => '0',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertDatabaseMissing('users', ['email' => 'shortpass@suluh.id']);
    }

    public function test_store_user_fails_validation_for_duplicate_email(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'role' => 'admin',
        ]);

        $existingUser = User::factory()->create([
            'email' => 'existing@suluh.id',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Duplicate User',
            'email' => 'existing@suluh.id',
            'password' => 'password123',
            'role' => 'user',
            'is_admin' => '0',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_admin_cannot_update_role_of_another_admin(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'role' => 'admin',
        ]);

        $anotherAdmin = User::factory()->create([
            'is_admin' => true,
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.users.update', $anotherAdmin->id), [
            'role' => 'mentor',
            'is_admin' => '0',
        ]);

        $response->assertSessionHas('error', 'Anda tidak dapat mengubah peran atau status pengguna administrator.');
        $anotherAdmin->refresh();
        $this->assertTrue($anotherAdmin->is_admin);
        $this->assertEquals('admin', $anotherAdmin->role);
    }

    public function test_admin_cannot_delete_another_admin(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'role' => 'admin',
        ]);

        $anotherAdmin = User::factory()->create([
            'is_admin' => true,
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $anotherAdmin->id));
        $response->assertSessionHas('error', 'Anda tidak dapat menghapus akun administrator.');
        $this->assertDatabaseHas('users', ['id' => $anotherAdmin->id]);
    }
}
