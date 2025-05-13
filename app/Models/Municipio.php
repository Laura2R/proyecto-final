<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    use HasFactory;

    protected $fillable = ['id_municipio', 'nombre'];

    public function nucleos()
    {
        return $this->hasMany(Nucleo::class, 'id_municipio', 'id_municipio');
    }

    public function paradas()
    {
        return $this->hasMany(Parada::class, 'id_municipio', 'id_municipio');
    }

    public function puntosVenta()
    {
        return $this->hasMany(PuntoVenta::class, 'id_municipio', 'id_municipio');
    }
}
