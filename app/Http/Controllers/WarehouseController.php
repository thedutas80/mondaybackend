<?php

namespace App\Http\Controllers;


use App\Http\Resources\WarehouseResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\WarehouseRequest;
use App\Services\WarehouseService;



class WarehouseController extends Controller
{
    //
    private $warehouseService;

    public function __construct(WarehouseService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
    }

    public function index()
    {
        $fields = ['id', 'name', 'photo', 'phone', 'address'];
        $warehouses = $this->warehouseService->getAll($fields ?: ['*']);
        return response()->json(WarehouseResource::collection($warehouses));
    }

    public function show($id)
    {
        try {
            $fields = ['id', 'name', 'photo', 'phone', 'address'];
            $warehouse = $this->warehouseService->getById($id, $fields);
            return response()->json(new WarehouseResource($warehouse));
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Warehouse not found'], 404);
        }
    }

    public function store(WarehouseRequest $request)
    {
        $data = $request->validated();
        $warehouse = $this->warehouseService->create($data);
        return response()->json(new WarehouseResource($warehouse), 201);
    }

    public function update(WarehouseRequest $request, $id)
    {
        try {
            $warehouse = $this->warehouseService->update($id, $request->validated());
            return response()->json(new WarehouseResource($warehouse));
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Warehouse not found'], 404);
        }
    }

    public function destroy($id)
    {
        try {
            $this->warehouseService->delete($id);
            return response()->json(['message' => 'Warehouse deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Warehouse not found'], 404);
        }
    }
}
