<?php

namespace App\Http\Controllers;

use App\Services\RoleService;
use App\Http\Resources\RoleResource;
use App\Http\Requests\RoleRequest;



class RoleController extends Controller
{
    //

    private RoleService $roleService;
    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index()
    {
        
        $fields = ['id', 'name' ];
        $role = $this->roleService->getAll($fields ?: ['*']);
        return response()->json(RoleResource::collection($role));
    }

    public function show($id)
    {
        $fields = ['id', 'name' ];
        $role = $this->roleService->getById($id, $fields ?: ['*']);
        return response()->json(new RoleResource($role));
    }

    public function store(RoleRequest $request)
    {
        $role = $this->roleService->create($request->validated());
        return response()->json(new RoleResource($role), 201);
    }

    public function update(RoleRequest $request, $id)
    {
        $role = $this->roleService->update($id, $request->validated());
        return response()->json(new RoleResource($role));
    }

    public function destroy($id)
    {
        $this->roleService->delete($id);
        return response()->json(null, 204);
    }   

    
}
