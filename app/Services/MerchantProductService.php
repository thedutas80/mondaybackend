<?php

namespace App\Services;

 
use App\Repositories\MerchantProductRepository;
use App\Repositories\WarehouseProductRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class MerchantProductService
{
    private MerchantProductRepository $merchantProductRepository;
    private WarehouseProductRepository $warehouseProductRepository;
   
    public function __construct(MerchantProductRepository $merchantProductRepository, WarehouseProductRepository $warehouseProductRepository)
    {
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
                throw ValidationException::withMessages(['product_id' => 'This product is already assigned to the merchant.']);
            }

            //kurangi stock
            $this->warehouseProductRepository->updateStock(
                $data['warehouse_id'],
                $data['product_id'],
                $warehouseProduct->stock - $data['stock']
            );

            return $this->merchantProductRepository->create(
                [
                    'merchant_id' => $data['merchant_id'],
                    'warehouse_id' => $data['warehouse_id'],
                    'product_id' => $data['product_id'],
                    'stock' => $data['stock']
                ]
            );

            });

    }

}
