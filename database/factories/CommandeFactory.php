<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Commande>
 */
class CommandeFactory extends Factory
{
    public function definition(): array
    {
        $ht     = $this->faker->randomElement([40000, 75000, 150000, 300000]);
        $aib    = round($ht * 0.05);
        $ttc    = $ht + $aib;
        $annee  = date('Y');
        $num    = str_pad($this->faker->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT);

        return [
            'numero_commande'       => "SDS-{$annee}-{$num}",
            'service_id'            => Service::factory(),
            'montant_fcfa'          => $ht,
            'tva_fcfa'              => $aib,
            'total_ttc_fcfa'        => $ttc,
            'montant_euro'          => round($ht / 655.96, 2),
            'client_nom'            => $this->faker->name(),
            'client_email'          => $this->faker->email(),
            'client_telephone'      => '+229' . $this->faker->numerify('########'),
            'client_entreprise'     => $this->faker->company(),
            'methode_paiement'      => $this->faker->randomElement(['mobile_money', 'fedapay', 'virement']),
            'statut'                => $this->faker->randomElement(['en_attente', 'payee', 'en_cours', 'livree']),
            'payment_token'         => null,
            'payment_transaction_id' => null,
            'paiement_at'           => null,
        ];
    }

    public function payee(): static
    {
        return $this->state(['statut' => 'payee', 'paiement_at' => now()]);
    }

    public function enAttente(): static
    {
        return $this->state(['statut' => 'en_attente']);
    }
}
