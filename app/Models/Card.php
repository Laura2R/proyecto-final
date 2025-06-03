<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Card extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'saldo'];

    protected $casts = [
        'saldo' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener el saldo formateado en euros
     */
    public function getSaldoFormateadoAttribute()
    {
        return number_format($this->saldo / 100, 2);
    }

    /**
     * Obtener el saldo en euros (float)
     */
    public function getSaldoEurosAttribute()
    {
        return $this->saldo / 100;
    }

    /**
     * Scope para obtener tarjetas con saldo positivo
     */
    public function scopeConSaldo($query)
    {
        return $query->where('saldo', '>', 0);
    }
}
