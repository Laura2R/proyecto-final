<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nucleo extends Model
{
    use HasFactory;

    protected $fillable = ['id_nucleo', 'id_municipio', 'id_zona', 'nombre'];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'id_municipio', 'id_municipio');
    }

    public function zona()
    {
        return $this->belongsTo(Zona::class, 'id_zona', 'id_zona');
    }

    public function paradas()
    {
        return $this->hasMany(Parada::class, 'id_nucleo', 'id_nucleo');
    }
}
