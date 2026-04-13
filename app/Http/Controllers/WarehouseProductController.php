<?php

namespace App\Http\Controllers;

use App\Services\WarehouseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\WarehouseProductRequest;

class WarehouseProductController extends Controller
{
    private WarehouseService $warehouseService;

    public function __construct(WarehouseService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
    }

    public function attach(Request $request, int $warehouseId): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'stock' => 'required|integer|min:1',
        ]);

        $this->warehouseService->attachProduct(
            $warehouseId,
            $request->input('product_id'),
            $request->input('stock')
        );

        return response()->json([
            'message' => 'Product attached to warehouse successfully.'
        ]);
    }


    public function detach(Request $request, int $warehouseId): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
        ]);

        $this->warehouseService->detachProduct(
            $warehouseId,
            $request->input('product_id')
        );

        return response()->json([
            'message' => 'Product detached from warehouse successfully.'
        ]);
    }

    public function updateStock(WarehouseProductRequest $request, int $warehouseId): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'stock' => 'required|integer|min:0',
        ]);

        $warehouseProduct = $this->warehouseService->updateProductStock(
            $warehouseId,
            $request->input('product_id'),
            $request->input('stock')
        );

        return response()->json([
            'message' => 'Warehouse product stock updated successfully.',
            'data' => $warehouseProduct
        ]);
    }
}