<?php
// app/Models/Horario.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_linea',
        'id_planificador',
        'fecha_inicio',
        'fecha_fin',
        'muestra_fecha_fin',
        'sentido',
        'horas',
        'frecuencia_acronimo',
        'observaciones',
        'demanda_horas',
        'nucleos',
        'bloques'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'muestra_fecha_fin' => 'boolean',
        'horas' => 'array',
        'nucleos' => 'array',
        'bloques' => 'array'
    ];

    public function linea()
    {
        return $this->belongsTo(Linea::class, 'id_linea', 'id_linea');
    }

    public function frecuencia()
    {
        return $this->belongsTo(Frecuencia::class, 'frecuencia_acronimo', 'acronimo');
    }
}
