<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zona extends Model
{
    use HasFactory;

    protected $fillable = ['id_zona', 'nombre', 'color'];

    public function nucleos()
    {
        return $this->hasMany(Nucleo::class, 'id_zona', 'id_zona');
    }

    public function paradas()
    {
        return $this->hasMany(Parada::class, 'id_zona', 'id_zona');
    }
}
