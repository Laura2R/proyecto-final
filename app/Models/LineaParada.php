<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineaParada extends Model
{
    use HasFactory;

    protected $table = 'linea_parada';

    protected $fillable = [
        'id_linea',
        'id_parada',
        'orden',
        'sentido'
    ];

    public function linea()
    {
        return $this->belongsTo(Linea::class, 'id_linea', 'id_linea');
    }

    public function parada()
    {
        return $this->belongsTo(Parada::class, 'id_parada', 'id_parada');
    }
}
