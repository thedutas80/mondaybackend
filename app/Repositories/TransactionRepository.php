<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Models\TransactionProduct;


class TransactionRepository
{
    public function getAll(array $fields)
    {
        return Transaction::select($fields)
            ->with(['TransactionProducts.Product', 'Merchant.keeper'])
            ->latest()
            ->paginate(10);
    }

    public function getById($id, array $fields)
    {
        return Transaction::select($fields)
            ->with(['TransactionProducts.Product', 'Merchant.keeper'])
            ->findOrFail($id);
    }

    public function create(array $data)
    {
        return Transaction::create($data);
    }

    public function update($id, array $data)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->update($data);
        return $transaction;
    }

    public function delete($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();
    }

    public function createTransactionProducts($transactionId, array $products)
    {
        foreach ($products as $product) {

            TransactionProduct::create([
                'transaction_id' => $transactionId,
                'product_id' => $product['product_id'],
                'quantity' => $product['quantity'],
                'price' => $product['price'],
                'sub_total' => $product['sub_total'],
            ]);
        }
    }

    public function getTransactionByMerchant($merchantId)
    {
        return Transaction::where('merchant_id', $merchantId)
            ->select(['*'])
            ->with(['TransactionProducts.Product', 'Merchant.keeper'])
            ->get();
    }
}
