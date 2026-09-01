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
        Service::factory()->count(3)->create(['actif' => true, 'categorie' => 'web']);
        Service::factory()->create(['actif' => false, 'categorie' => 'excel']);
    }

    /** @test */
    public function it_returns_only_active_services()
    {
        $response = $this->getJson('/api/services');

        $response->assertOk()
            ->assertJsonPath('success', true);

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

    /** @test */
    public function an_inactive_service_is_not_returned_by_category_endpoint()
    {
        $inactive = Service::factory()->create(['actif' => false, 'categorie' => 'materiel']);

        $response = $this->getJson('/api/services/categorie/materiel');

        $response->assertOk();
        $this->assertCount(0, $response->json('data'));
        $this->assertFalse(collect($response->json('data'))->contains('id', $inactive->id));
    }

    /** @test */
    public function it_supports_the_materials_category()
    {
        Service::factory()->create(['actif' => true, 'categorie' => 'materiel']);

        $response = $this->getJson('/api/services/categorie/materiel');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
        $response->assertJsonPath('data.0.categorie', 'materiel');
    }

    /** @test */
    public function it_does_not_expose_an_inactive_service_through_single_service_endpoint()
    {
        $inactive = Service::factory()->create(['actif' => false]);

        $this->getJson("/api/services/{$inactive->id}")
            ->assertNotFound();
    }
}
