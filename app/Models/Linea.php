<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Linea extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_linea', 'codigo', 'nombre', 'modo', 'operadores',
        'hay_noticias', 'termometro_ida', 'termometro_vuelta',
        'polilinea', 'color', 'tiene_ida', 'tiene_vuelta',
        'pmr', 'concesion', 'observaciones'
    ];

    protected $casts = [
        'polilinea' => 'array',
        'hay_noticias' => 'boolean',
        'tiene_ida' => 'boolean',
        'tiene_vuelta' => 'boolean'
    ];

    public function paradas()
    {
        return $this->belongsToMany(Parada::class, 'linea_parada', 'id_linea', 'id_parada')
            ->withPivot('orden', 'sentido')
            ->withTimestamps();
    }

    public function paradasIda()
    {
        return $this->belongsToMany(Parada::class, 'linea_parada', 'id_linea', 'id_parada')
            ->withPivot('orden')
            ->wherePivot('sentido', 'ida')
            ->orderBy('linea_parada.orden')
            ->withTimestamps();
    }

    public function paradasVuelta()
    {
        return $this->belongsToMany(Parada::class, 'linea_parada', 'id_linea', 'id_parada')
            ->withPivot('orden')
            ->wherePivot('sentido', 'vuelta')
            ->orderBy('linea_parada.orden')
            ->withTimestamps();
    }

    public function horarios()
    {
        return $this->hasMany(Horario::class, 'id_linea', 'id_linea');
    }

    public function horariosIda()
    {
        return $this->horarios()->where('sentido', 'ida');
    }

    public function horariosVuelta()
    {
        return $this->horarios()->where('sentido', 'vuelta');
    }

    public function servicios()
    {
        return $this->hasMany(Servicio::class, 'id_linea', 'id_linea');
    }

    public function usuariosFavoritos()
    {
        return $this->belongsToMany(User::class, 'favoritos_lineas', 'id_linea', 'user_id')
            ->withTimestamps();
    }
}
