<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    public function definition(): array
    {
        $prix = $this->faker->randomElement([40000, 75000, 100000, 150000, 200000, 300000]);

        return [
            'nom'         => $this->faker->words(3, true),
            'description' => $this->faker->sentence(10),
            'icone'       => 'fas fa-code',
            'prix_fcfa'   => $prix,
            'prix_euro'   => round($prix / 655.96, 2),
            'duree'       => $this->faker->randomElement(['3-5 jours', '7-10 jours', '2-3 semaines']),
            'categorie'   => $this->faker->randomElement(['web', 'excel', 'survey', 'formation']),
            'popular'     => $this->faker->boolean(20),
            'actif'       => true,
            'features'    => ['Feature 1', 'Feature 2', 'Feature 3'],
        ];
    }
}
