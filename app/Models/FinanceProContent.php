<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceProContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'subtitle', 'description', 'features', 'benefits',
        'faq', 'hero_image', 'demo_url', 'price_fcfa', 'price_euro',
        'price_period', 'published', 'published_at',
    ];

    protected $casts = [
        'features' => 'array',
        'benefits' => 'array',
        'faq' => 'array',
        'price_fcfa' => 'decimal:2',
        'price_euro' => 'decimal:2',
        'published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function scopePublished($query)
    {
        return $query->where('published', true);
    }
}
