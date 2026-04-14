<?php

namespace App\Repositories;

use App\Models\MerchantProduct;
use Illuminate\Validation\ValidationException;


class MerchantProductRepository 
{
    public function create(array $data)
    {
        return MerchantProduct::create($data);
    }

    public function getByMerchantAndProduct(int $merchantId, int $productId, array $fields = ['*'])
    {
        return MerchantProduct::where('merchant_id', $merchantId)
            ->where('product_id', $productId)
            ->first();
    }

     public function updateStock(int $merchantId, int $productId, int $stock)
    {
      $merchanProduct = $this->getByMerchantAndProduct($merchantId, $productId);

        if (!$merchanProduct) {
            throw ValidationException::withMessages(['product_id' => 'MerchantProduct not found for the given merchant and product IDs.']);
        }

        $merchanProduct->update(['stock' => $stock]);

        return $merchanProduct;
    }


}

?>