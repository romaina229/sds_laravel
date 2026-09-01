<?php

namespace Tests\Feature;

use App\Models\FinanceProContent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FinanceProApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function public_endpoint_returns_404_when_nothing_is_published(): void
    {
        $this->getJson('/api/finance-pro')
            ->assertNotFound()
            ->assertJsonPath('success', false);
    }

    /** @test */
    public function public_endpoint_returns_published_content(): void
    {
        $content = FinanceProContent::factory()->create(['published' => true]);

        $this->getJson('/api/finance-pro')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $content->id);
    }

    /** @test */
    public function admin_can_create_and_publish_finance_pro_content(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/admin/finance-pro', [
            'title' => 'Finance Pro',
            'description' => 'Solution de gestion financière pour ONG.',
            'features' => ['Comptabilité', 'Budgets'],
            'published' => false,
        ]);

        $response->assertCreated();
        $id = $response->json('data.id');

        $this->postJson("/api/admin/finance-pro/{$id}/publish")
            ->assertOk()
            ->assertJsonPath('data.published', true);
    }

    /** @test */
    public function publishing_one_content_unpublishes_the_other(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $first = FinanceProContent::factory()->create(['published' => true]);
        $second = FinanceProContent::factory()->create(['published' => false]);

        $this->postJson("/api/admin/finance-pro/{$second->id}/publish")
            ->assertOk();

        $this->assertDatabaseHas('finance_pro_contents', [
            'id' => $first->id,
            'published' => false,
        ]);

        $this->assertDatabaseHas('finance_pro_contents', [
            'id' => $second->id,
            'published' => true,
        ]);
    }

    /** @test */
    public function non_admin_cannot_manage_finance_pro(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Sanctum::actingAs($user);

        $this->getJson('/api/admin/finance-pro')->assertForbidden();
    }
}
