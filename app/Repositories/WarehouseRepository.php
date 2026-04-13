<?php

namespace App\Repositories;


class WarehouseRepository
{
    //

    public function getAll(array $fields)
    {
        return \App\Models\Warehouse::select($fields)->with(['products.category'])->latest()->paginate(10);
    }

    public function getById($id, array $fields)
    {
        return \App\Models\Warehouse::select($fields)->with(['products.category'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return \App\Models\Warehouse::create($data);
    }

    public function update(int $id, array $data)
    {
        $warehouse = \App\Models\Warehouse::findOrFail($id);
        $warehouse->update($data);
        return $warehouse;
    }

        public function delete(int $id)
    {
        $warehouse = \App\Models\Warehouse::findOrFail($id);
        $warehouse->delete();

    }


}

?>