<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;


use Illuminate\Database\Eloquent\Model;

class Frecuencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_frecuencia', 'acronimo', 'nombre'
    ];

    public function horarios()
    {
        return $this->hasMany(Horario::class, 'id_frecuencia', 'id_frecuencia');
    }
}
