<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function modifyRole(User $user)
    {
        return $user->isAdmin();
    }
    public function delete(User $user, User $model)
    {
        return $user->isAdmin();
    }
}