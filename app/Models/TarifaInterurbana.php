<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TarifaInterurbana extends Model
{
    use HasFactory;

    protected $table = 'tarifas_interurbanas';

    protected $fillable = [
        'saltos',
        'bs',
        'tarjeta'
    ];

    protected $casts = [
        'saltos' => 'integer',
        'bs' => 'decimal:7',
        'tarjeta' => 'decimal:7'
    ];


    // Obtener tarifa por número de saltos
    public static function getPorSaltos(int $saltos): ?self
    {
        return self::where('saltos', $saltos)->first();
    }


    // Obtener todas las tarifas ordenadas por saltos
    public static function getOrdenadas()
    {
        return self::orderBy('saltos')->get();
    }
}
