<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PuntoVenta extends Model
{
    use HasFactory;

    protected $table = 'puntos_venta';

    protected $fillable = [
        'id_punto', 'id_municipio',  'direccion', 'municipio',
        'tipo', 'latitud', 'longitud', 'horario', 'servicios'
    ];

    protected $casts = [
        'servicios' => 'array'
    ];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'id_municipio', 'id_municipio');
    }
}
