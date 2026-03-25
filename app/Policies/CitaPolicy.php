<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Cita;
use App\Models\User;

class CitaPolicy
{

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Cita $cita): bool
    {
        if ($user->hasRole(Role::ADMIN)) {
            return true;
        }

        return $user->id == $cita->user_id;
    }
}
