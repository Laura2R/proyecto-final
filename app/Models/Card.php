<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Card extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'saldo','numero_tarjeta'];

    protected $casts = [
        'saldo' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();// Para generar el número automático

        static::creating(function ($card) {
            if (!$card->numero_tarjeta) {
                $card->numero_tarjeta = self::generarNumeroTarjeta();
            }
        });
    }

    public static function generarNumeroTarjeta()
    {
        do {
            // Generar 8 dígitos aleatorios después del prefijo "09"
            $numeroAleatorio = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
            $numeroCompleto = '09' . $numeroAleatorio;
        } while (self::where('numero_tarjeta', $numeroCompleto)->exists());

        return $numeroCompleto;
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
