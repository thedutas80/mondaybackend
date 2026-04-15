<?php

namespace App\Services;


use App\Repositories\UserRoleRepository;



class userRoleService
{
    private UserRoleRepository $userRoleRepository;

    public function __construct(UserRoleRepository $userRoleRepository)
    {
        $this->userRoleRepository = $userRoleRepository;
    }

    public function assignRole(int $userId, int $roleId)
    {
        return $this->userRoleRepository->assignRole($userId, $roleId);
    }

    public function removeRoleFromUser(int $userId, int $roleId)
    {
        return $this->userRoleRepository->removeRoleFromUser($userId, $roleId);
    }

    public function listUserRoles(int $userId)
    {
        return $this->userRoleRepository->getUserRoles($userId);
    }

}