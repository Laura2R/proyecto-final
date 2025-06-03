<?php

namespace App\Policies;

use App\Models\Transaccion;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransaccionPolicy
{
    use HandlesAuthorization;

    public function download(User $user, Transaccion $transaccion)
    {
        return $user->id === $transaccion->user_id;
    }
}
