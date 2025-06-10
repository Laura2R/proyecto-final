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

    // Boot del modelo para generar número automáticamente

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($card) {
            if (!$card->numero_tarjeta) {
                $card->numero_tarjeta = self::generarNumeroTarjeta();
            }
        });
    }


    // Generar número único de tarjeta de 10 dígitos empezando por 09
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


    //Obtener el saldo formateado en euros
    public function getSaldoFormateadoAttribute()
    {
        return number_format($this->saldo / 100, 2);
    }


    //Obtener el saldo en euros (float)

    public function getSaldoEurosAttribute()
    {
        return $this->saldo / 100;
    }


    // Scope para obtener tarjetas con saldo positivo
    public function scopeConSaldo($query)
    {
        return $query->where('saldo', '>', 0);
    }
}
