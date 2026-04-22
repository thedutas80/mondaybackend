<?php

namespace App\Http\Controllers;


use App\Services\UserService;
use App\Http\Resources\UserResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $fields = [
            'id',
            'name',
            'email',
            'password',
            'phone',
            'photo'
        ];
        $users = $this->userService->getAll($fields ?: ['*']);
        return response()->json(UserResource::collection($users));
    }

    public function show($id)
    {
        try {
            $fields = [
                'id',
                'name',
                'email',
                'password',
                'phone',
                'photo'
            ];
            $user = $this->userService->getById($id, $fields);
            return response()->json(new UserResource($user));
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'User not found'], 404);
        }
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();
        $user = $this->userService->create($data);
        return response()->json(new UserResource($user), 201);
    }

    public function update(UserRequest $request, $id)
    {
        try {
            $user = $this->userService->update($id, $request->validated());
            return response()->json(new UserResource($user));
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'User not found'], 404);
        }
    }

    public function destroy($id)
    {
        try {
            $this->userService->delete($id);
            return response()->json(['message' => 'User deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'User not found'], 404);
        }
    }
}
