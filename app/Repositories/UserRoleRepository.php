<?php

namespace App\Repositories;

use Spatie\Permission\Models\Role;
use App\Models\User;


class UserRoleRepository
{
    public function assignRole(int $userId, int $roleId)
    {
        $user = User::findOrFail($userId);
        $role = Role::findOrFail($roleId);

        $user->assignRole($role);
        return $user;
    }

    public function removeRoleFromUser(int $userId, int $roleId)
    {
        $user = User::findOrFail($userId);
        $role = Role::findOrFail($roleId);

        $user->removeRole($role);
        return $user;
    }

    public function getUserRoles(int $userId)
    {
        $user = User::findOrFail($userId);
        return $user->roles;
    }
}