<?php

namespace App\Services;

use App\Repositories\MerchantProductRepository;
use App\Repositories\MerchantRepository;
use App\Repositories\WarehouseProductRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MerchantProductService
{
    private MerchantProductRepository $merchantProductRepository;
    private MerchantRepository $merchantRepository;
    private WarehouseProductRepository $warehouseProductRepository;

    public function __construct(
        MerchantProductRepository $merchantProductRepository,
        WarehouseProductRepository $warehouseProductRepository
    ) {
        $this->merchantProductRepository = $merchantProductRepository;
        $this->warehouseProductRepository = $warehouseProductRepository;
    }

    public function assignProductToMerchant(array $data)
    {
        return DB::transaction(function () use ($data) {
            $warehouseProduct = $this->warehouseProductRepository->getWarehouseAndProduct(
                $data['warehouse_id'],
                $data['product_id']
            );

            if (!$warehouseProduct || $warehouseProduct->stock < $data['stock']) {
                throw new \Exception('Not enough stock in warehouse');
            }

            $existingMerchantProduct = $this->merchantProductRepository->getByMerchantAndProduct(
                $data['merchant_id'],
                $data['product_id']
            );

            if ($existingMerchantProduct) {
                throw ValidationException::withMessages([
                    'product_id' => 'This product is already assigned to the merchant.'
                ]);
            }

            //kurangi stock
            $this->warehouseProductRepository->updateStock(
                $data['warehouse_id'],
                $data['product_id'],
                $warehouseProduct->stock - $data['stock']
            );

            return $this->merchantProductRepository->create([
                'merchant_id' => $data['merchant_id'],
                'warehouse_id' => $data['warehouse_id'],
                'product_id' => $data['product_id'],
                'stock' => $data['stock']
            ]);
        });
    }

    public function UpdateStock(int $merchantId, int $productId, int $newStock, int $warehouseid = null)
    {
        return DB::transaction(function () use ($merchantId, $productId, $newStock, $warehouseid) {

            $existing = $this->merchantProductRepository->getByMerchantAndProduct($merchantId, $productId);

            if (!$existing) {
                throw ValidationException::withMessages([
                    'product_id' => 'This product is not assigned to the merchant.'
                ]);
            }

            if (!$warehouseid) {
                throw ValidationException::withMessages([
                    'warehouse_id' => 'Warehouse ID is required to update stock.'
                ]);
            }

            $currentStock = $existing->stock;

            if ($newStock > $currentStock) {
                // Need to increase stock, check warehouse availability
                $diff = $newStock - $currentStock;

                $warehouseProduct = $this->warehouseProductRepository->getWarehouseAndProduct(
                    $warehouseid,
                    $productId
                );

                if (!$warehouseProduct || $warehouseProduct->stock < $diff) {
                    throw ValidationException::withMessages([
                        'stock' => 'Not enough stock in warehouse to increase merchant stock.'
                    ]);
                }

                $this->warehouseProductRepository->updateStock(
                    $warehouseid,
                    $productId,
                    $warehouseProduct->stock - $diff
                );
            }
            if ($newStock < $currentStock) {
                // Need to decrease stock, return to warehouse
                $diff = $currentStock - $newStock;

                $warehouseProduct = $this->warehouseProductRepository->getWarehouseAndProduct(
                    $warehouseid,
                    $productId
                );

                if (!$warehouseProduct) {
                    throw ValidationException::withMessages([
                        'warehouse_id' => 'Warehouse product not found.'
                    ]);
                }

                $this->warehouseProductRepository->updateStock(
                    $warehouseid,
                    $productId,
                    $warehouseProduct->stock + $diff
                );
            }
            return $this->merchantProductRepository->updateStock($merchantId, $productId, $newStock);
        }); // <- ini yang tadi kurang
    }

    public function RemoveProductFromMerchant(int $merchantId, int $productId)
    {
        $merchant = $this->merchantRepository->getById($merchantId, $fields ?? ['*']);

        $exists = $this->merchantProductRepository->getByMerchantAndProduct(
            $merchantId,
            $productId
        );

        if (!$exists) {
            throw ValidationException::withMessages([
                'product_id' => 'This product is not assigned to the merchant.'
            ]);
        }

        $merchant->products()->detach($productId);
    }
}