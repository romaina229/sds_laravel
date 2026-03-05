<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Parametre extends Model
{
    protected $fillable = ['cle', 'valeur', 'groupe'];

    public static function get(string $cle, mixed $default = null): mixed
    {
        return Cache::remember("param_{$cle}", 3600, function () use ($cle, $default) {
            $param = static::where('cle', $cle)->first();
            return $param ? $param->valeur : $default;
        });
    }

    public static function set(string $cle, mixed $valeur, string $groupe = 'general'): void
    {
        static::updateOrCreate(
            ['cle' => $cle],
            ['valeur' => $valeur, 'groupe' => $groupe]
        );
        Cache::forget("param_{$cle}");
    }
}
