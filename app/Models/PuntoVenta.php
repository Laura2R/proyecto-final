<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PuntoVenta extends Model
{
    use HasFactory;

    protected $table = 'puntos_venta';

    protected $fillable = [
        'id_punto', 'id_municipio','id_nucleo', 'direccion',
        'tipo', 'latitud', 'longitud'
    ];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'id_municipio', 'id_municipio');
    }
    public function nucleo()
    {
        return $this->belongsTo(Nucleo::class, 'id_nucleo', 'id_nucleo');
    }
}
