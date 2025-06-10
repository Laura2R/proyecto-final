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
}
