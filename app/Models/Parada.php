<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parada extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_parada', 'id_nucleo', 'id_municipio', 'id_zona',
        'nombre', 'latitud', 'longitud', 'modos'
    ];

    protected $casts = [
        'sentido' => 'integer',
        'orden' => 'integer'
    ];

    public function nucleo()
    {
        return $this->belongsTo(Nucleo::class, 'id_nucleo', 'id_nucleo');
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'id_municipio', 'id_municipio');
    }

    public function zona()
    {
        return $this->belongsTo(Zona::class, 'id_zona', 'id_zona');
    }

    public function lineas()
    {
        return $this->belongsToMany(Linea::class, 'linea_parada', 'id_parada', 'id_linea')
            ->withPivot('orden', 'sentido')
            ->withTimestamps();
    }

    public function servicios()
    {
        return $this->hasMany(Servicio::class, 'id_parada', 'id_parada');
    }

    public function usuariosFavoritos()
    {
        return $this->belongsToMany(User::class, 'favoritos_paradas', 'id_parada', 'user_id')
            ->withTimestamps();
    }
}
