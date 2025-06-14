<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function lineasFavoritas()
    {
        return $this->belongsToMany(Linea::class, 'favoritos_lineas', 'user_id', 'id_linea', 'id', 'id_linea')
            ->withTimestamps();
    }

    public function paradasFavoritas()
    {
        return $this->belongsToMany(Parada::class, 'favoritos_paradas', 'user_id', 'id_parada', 'id', 'id_parada')
            ->withTimestamps();
    }


    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    public function transacciones()
    {
        return $this->hasMany(Transaccion::class);
    }
}
