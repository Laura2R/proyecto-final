<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'monto',
        'metodo',
        'estado',
        'detalles'
    ];

    protected $casts = [
        'detalles' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
