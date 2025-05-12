<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_parada', 'id_linea', 'hora', 'destino', 'sentido'
    ];

    protected $casts = [
        'hora' => 'datetime'
    ];

    public function parada()
    {
        return $this->belongsTo(Parada::class, 'id_parada', 'id_parada');
    }

    public function linea()
    {
        return $this->belongsTo(Linea::class, 'id_linea', 'id_linea');
    }
}
