<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_linea', 'sentido', 'tipo_dia', 'horas', 'id_frecuencia'
    ];

    protected $casts = [
        'horas' => 'array'
    ];

    public function linea()
    {
        return $this->belongsTo(Linea::class, 'id_linea', 'id_linea');
    }

    public function frecuencia()
    {
        return $this->belongsTo(Frecuencia::class, 'id_frecuencia', 'id_frecuencia');
    }
}
