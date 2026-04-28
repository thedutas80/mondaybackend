<?php

namespace App\Services;

use App\Repositories\TransactionRepository;
use App\Repositories\MerchantProductRepository;
use App\Repositories\ProductRepository;
use App\Repositories\MerchantRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TransactionService
{
    private TransactionRepository $transactionRepository;
    private MerchantProductRepository $merchantProductRepository;
    private ProductRepository $productRepository;
    private MerchantRepository $merchantRepository;


    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
        $this->merchantProductRepository = new MerchantProductRepository();
        $this->productRepository = new ProductRepository();
        $this->merchantRepository = new MerchantRepository();
    }


    public function getAll(array $fields = ['*'])
    {
        return $this->transactionRepository->getAll($fields);
    }

    public function getById($id, array $fields = ['*'])
    {
        return $this->transactionRepository->getById($id, $fields);
    }

    public function getByTransactionId(int $id, array $fields)
    {
        $transaction = $this->transactionRepository->getById($id, $fields ?? ['*']);
        if (!$transaction)
            throw ValidationException::withMessages(['transaction_id' => 'not found']);
        return $transaction;
    }

    public function getTransactionByMerchant(int $merchantId)
    {
        return $this->transactionRepository->getTransactionByMerchant($merchantId);
    }

    // public function createTransaction(array $data)
    // {
    //     DB::transaction(function () use ($data) {

    //         $merchant = $this->merchantRepository->getById($data['merchant_id'], ['id', 'keeper_id']);

    //         if (!$merchant) {
    //             throw ValidationException::withMessages(['merchant_id' => 'Merchant not found']);
    //         }

    //         if (Auth::id() !== $merchant->keeper_id) {
    //             throw ValidationException::withMessages(['autorization' => 'Unauthorized! You are not the keeper of this merchant']);
    //         }

    //         $product = [];
    //         $sub_total = 0;

    //         foreach ($data['products'] as $productdata) {

    //             $merchantProduct = $this->merchantProductRepository
    //                 ->getByMerchantAndProduct($data['merchant_id'], $productdata['product_id']);

    //             if (!$merchantProduct || $merchantProduct->stock < $productdata['quantity']) {
    //                 throw ValidationException::withMessages([
    //                     'stock' => 'Insufficient stock for product ID: ' . $productdata['product_id']
    //                 ]);
    //             }

    //             $product = $this->productRepository
    //                 ->getById($productdata['product_id'], ['price']);

    //             if (!$product) {
    //                 throw ValidationException::withMessages([
    //                     'product_id' => 'Product not found'
    //                 ]);
    //             }

    //             $price = $product->price;
    //             $productSubtotal = $productdata['quantity'] * $price;
    //             $sub_total += $productSubtotal;

    //             $product[] = [
    //                 'product_id' => $productdata['product_id'],
    //                 'quantity' => $productdata['quantity'],
    //                 'price' => $price,
    //                 'sub_total' => $productSubtotal
    //             ];
    //             $newStock = max(0, $merchantProduct->stock - $productdata['quantity']);
    //             $this->merchantProductRepository->updateStock($data['merchant_id'], $productdata['product_id'], $newStock);
    //         }
    //         $taxTotal = $sub_total * 0.1;
    //         $grandTotal = $sub_total + $taxTotal;

    //         $transaction = $this->transactionRepository->create([
    //             'name' => $data['name'],
    //             'phone' => $data['phone'],
    //             'merchant_id' => $data['merchant_id'],
    //             'sub_total' => $sub_total,
    //             'taxTotal' => $taxTotal,
    //             'grandTotal' => $grandTotal

    //         ]);

    //         $this->transactionRepository->createTransactionProducts($transaction->id, $product);
    //         return $transaction->fresh();
    //     });
    // }


    public function createTransaction(array $data)
    {
        return DB::transaction(function () use ($data) {
            $merchant = $this->merchantRepository->getById($data['merchant_id'], ['id', 'keeper_id']);

            if (!$merchant) {
                throw ValidationException::withMessages(['merchant_id' => 'Merchant not found']);
            }

            if (Auth::id() !== $merchant->keeper_id) {
                throw ValidationException::withMessages(['autorization' => 'Unauthorized!']);
            }

            $productsList = []; // Ganti nama agar tidak bentrok
            $sub_total = 0;

            foreach ($data['products'] as $productdata) {
                $merchantProduct = $this->merchantProductRepository
                    ->getByMerchantAndProduct($data['merchant_id'], $productdata['product_id']);

                if (!$merchantProduct || $merchantProduct->stock < $productdata['quantity']) {
                    throw ValidationException::withMessages([
                        'stock' => 'Insufficient stock for product ID: ' . $productdata['product_id']
                    ]);
                }

                // Gunakan nama variabel lain, misalnya $p atau $item
                $item = $this->productRepository
                    ->getById($productdata['product_id'], ['price']);

                if (!$item) {
                    throw ValidationException::withMessages([
                        'product_id' => 'Product not found'
                    ]);
                }

                $price = $item->price;
                $productSubtotal = $productdata['quantity'] * $price;
                $sub_total += $productSubtotal;

                // Masukkan ke array list
                $productsList[] = [
                    'product_id' => $productdata['product_id'],
                    'quantity' => $productdata['quantity'],
                    'price' => $price,
                    'sub_total' => $productSubtotal
                ];

                $newStock = max(0, $merchantProduct->stock - $productdata['quantity']);
                $this->merchantProductRepository->updateStock($data['merchant_id'], $productdata['product_id'], $newStock);
            }

            $taxTotal = $sub_total * 0.1;
            $grandTotal = $sub_total + $taxTotal;

            $transaction = $this->transactionRepository->create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'merchant_id' => $data['merchant_id'],
                'sub_total' => $sub_total,
                'tax_total' => $taxTotal,
                'grand_total' => $grandTotal,
            ]);

            // Gunakan variabel list yang tadi
            $this->transactionRepository->createTransactionProducts($transaction->id, $productsList);

            return $transaction->fresh();
        });
    }
}
