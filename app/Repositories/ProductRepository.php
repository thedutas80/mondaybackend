<?php

namespace App\Repositories;
use App\Models\Product;

class ProductRepository 
{
    //

    public function getAll(array $fields)
    {
        return Product::select($fields)->latest()->paginate(10)->with('category');
    }

    public function getById($id, array $fields)
    {
        return Product::select($fields)->findOrFail($id)->with('category');
    }

    public function create(array $data)
    {
        return Product::create($data);
    }

    public function update(int $id, array $data)
    {
        $product = Product::findOrFail($id);
        $product->update($data);
        return $product;
    }

        public function delete(int $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

    }


}

?>