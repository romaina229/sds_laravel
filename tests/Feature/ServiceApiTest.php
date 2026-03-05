<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Créer quelques services de test
        Service::factory()->count(3)->create(['actif' => true, 'categorie' => 'web']);
        Service::factory()->create(['actif' => false, 'categorie' => 'excel']);
    }

    /** @test */
    public function it_returns_only_active_services()
    {
        $response = $this->getJson('/api/services');

        $response->assertOk()
                 ->assertJsonPath('success', true);

        // Seulement les 3 actifs
        $this->assertCount(3, $response->json('data'));
    }

    /** @test */
    public function it_filters_services_by_category()
    {
        Service::factory()->create(['actif' => true, 'categorie' => 'formation']);

        $response = $this->getJson('/api/services/categorie/formation');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    /** @test */
    public function it_returns_single_service()
    {
        $service = Service::factory()->create(['actif' => true]);

        $this->getJson("/api/services/{$service->id}")
             ->assertOk()
             ->assertJsonPath('data.id', $service->id);
    }
}
