<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'email'    => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role'     => 'admin',
        ]);
    }

    /** @test */
    public function admin_can_login_with_valid_credentials()
    {
        $response = $this->postJson('/api/auth/login', [
            'email'    => 'admin@test.com',
            'password' => 'password123',
        ]);

        $response->assertOk()
                 ->assertJsonPath('success', true)
                 ->assertJsonStructure(['token', 'user']);
    }

    /** @test */
    public function login_fails_with_wrong_password()
    {
        $this->postJson('/api/auth/login', [
            'email'    => 'admin@test.com',
            'password' => 'wrong',
        ])->assertStatus(401);
    }

    /** @test */
    public function non_admin_cannot_access_admin_routes()
    {
        $user = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
             ->getJson('/api/admin/stats')
             ->assertStatus(403);
    }

    /** @test */
    public function admin_can_access_dashboard_stats()
    {
        $token = $this->admin->createToken('admin-token', ['admin'])->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
             ->getJson('/api/admin/stats')
             ->assertOk()
             ->assertJsonPath('success', true)
             ->assertJsonStructure(['data' => [
                 'total_commandes', 'commandes_payees', 'revenus_total',
             ]]);
    }

    /** @test */
    public function admin_can_logout()
    {
        $token = $this->admin->createToken('admin-token', ['admin'])->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
             ->postJson('/api/admin/auth/logout')
             ->assertOk();
    }
}
