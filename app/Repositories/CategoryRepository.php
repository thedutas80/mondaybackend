<?php

namespace App\Repositories;


class CategoryRepository 
{
    //

    public function getAll(array $fields)
    {
        return \App\Models\Category::select($fields)->latest()->paginate(10);
    }

    public function getById($id, array $fields)
    {
        return \App\Models\Category::select($fields)->findOrFail($id);
    }

    public function create(array $data)
    {
        return \App\Models\Category::create($data);
    }

    public function update(int $id, array $data)
    {
        $category = \App\Models\Category::findOrFail($id);
        $category->update($data);
        return $category;
    }

        public function delete(int $id)
    {
        $category = \App\Models\Category::findOrFail($id);
        $category->delete();

    }


}

?>