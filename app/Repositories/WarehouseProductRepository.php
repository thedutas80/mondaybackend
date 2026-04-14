<?php

namespace App\Repositories;

use App\Models\WarehouseProduct;
use Illuminate\Validation\ValidationException;

class WarehouseProductRepository
{
    public function getWarehouseAndProduct(int $warehouseId, int $productId): ?WarehouseProduct
    {
        return WarehouseProduct::where('warehouse_id', $warehouseId)
            ->where('product_id', $productId)
            ->first();
    }

    public function updateStock(int $warehouseId, int $productId, int $stock): WarehouseProduct
    {
        $warehouseProduct = $this->getWarehouseAndProduct($warehouseId, $productId);

        if (!$warehouseProduct) {
            throw ValidationException::withMessages([
                'message' => 'Warehouse product not found.'
            ]);
        }

        $warehouseProduct->update([
            'stock' => $stock
        ]);

        return $warehouseProduct;
    }
}