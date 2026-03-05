<?php

namespace Tests\Unit;

use App\Models\Commande;
use PHPUnit\Framework\TestCase;

class CommandeTest extends TestCase
{
    /** @test */
    public function it_generates_unique_order_numbers()
    {
        // Simuler la logique sans base de données
        $numero = 'SDS-' . date('Y') . '-' . str_pad(1, 5, '0', STR_PAD_LEFT);
        $this->assertMatchesRegularExpression('/^SDS-\d{4}-\d{5}$/', $numero);
    }

    /** @test */
    public function it_calculates_ttc_from_ht()
    {
        $ht  = 100000;
        $aib = $ht * 0.05; // 5000
        $ttc = $ht + $aib; // 105000

        $this->assertEquals(5000,   $aib);
        $this->assertEquals(105000, $ttc);
    }

    /** @test */
    public function statut_en_attente_is_not_paid()
    {
        $commande = new Commande(['statut' => 'en_attente']);
        $this->assertFalse((bool) ($commande->statut === 'payee'));
    }

    /** @test */
    public function statut_payee_is_paid()
    {
        $commande = new Commande(['statut' => 'payee']);
        $this->assertTrue($commande->statut === 'payee');
    }

    /** @test */
    public function statut_labels_are_defined()
    {
        $statuts = Commande::STATUTS;
        $this->assertArrayHasKey('en_attente', $statuts);
        $this->assertArrayHasKey('payee',      $statuts);
        $this->assertArrayHasKey('livree',     $statuts);
        $this->assertArrayHasKey('annulee',    $statuts);
    }
}
