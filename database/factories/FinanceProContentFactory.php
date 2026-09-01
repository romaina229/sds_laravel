<?php

namespace Database\Factories;

use App\Models\FinanceProContent;
use Illuminate\Database\Eloquent\Factories\Factory;

class FinanceProContentFactory extends Factory
{
    protected $model = FinanceProContent::class;

    public function definition(): array
    {
        $title = 'Finance Pro';

        return [
            'title' => $title,
            'slug' => $this->faker->unique()->slug(),
            'subtitle' => 'Gestion financière professionnelle pour les organisations.',
            'description' => $this->faker->paragraph(),
            'features' => ['Budgets', 'Comptabilité', 'Rapports'],
            'benefits' => ['Gain de temps', 'Meilleur suivi financier'],
            'faq' => [
                ['question' => 'Finance Pro fonctionne-t-il hors connexion ?', 'answer' => 'Oui, selon la configuration de l’application.'],
            ],
            'hero_image' => null,
            'demo_url' => null,
            'price_fcfa' => 12670,
            'price_euro' => null,
            'price_period' => 'mois',
            'published' => false,
            'published_at' => null,
        ];
    }
}
