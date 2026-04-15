<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Services\UserRoleService;
use App\Http\Requests\UserRoleRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserRoleController extends Controller
{
    private UserRoleService $userRoleService;

    public function __construct(UserRoleService $userRoleService)
    {
        $this->userRoleService = $userRoleService;
    }

    public function assignRole(UserRoleRequest $request)
    {
        $user = $this->userRoleService->assignRole(
            $request->validated()['user_id'],
            $request->validated()['role_id']
        );
        return response()->json(
            [
                'message' => 'Role assigned successfully',
                'data' => $user
            ]
        );
    }

    public function removeRole(UserRoleRequest $request, int $userId, int $roleId)
    {
        $user = $this->userRoleService->removeRoleFromUser(
            $request->validated()['user_id'],
            $request->validated()['role_id']
        );
        return response()->json(
            [
                'message' => 'Role removed successfully',
                'data' => $user
            ]
        );
    }

    public function listRoles(UserRoleRequest $request, int $userId)
    {
        try {
            $roles = $this->userRoleService->listUserRoles($userId);
            return response()->json(
                [
                    'user_id' => $userId,
                    'roles' => $roles
                ]
            );
        } catch (ModelNotFoundException $e) {   
            return response()->json(
                [
                    'message' => 'User not found',
                    'data' => null
                ],
                404
            );
        }
    }
}
