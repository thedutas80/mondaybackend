<?php

namespace App\Http\Controllers;


use App\Http\Resources\CategoryResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\CategoryRequest;  


class CategoryController extends Controller
{
    //
    private $categoryService;

    public function __construct(\App\Services\CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $fields = ['id', 'name', 'photo', 'tagline'];
        $categories = $this->categoryService->getAll($fields ?: ['*']);
        return response()->json(CategoryResource::collection($categories));
    }

    public function show($id)
    {
       try{
        $fields = ['id', 'name', 'photo', 'tagline'];
        $category = $this->categoryService->getById($id, $fields);
        return response()->json(new CategoryResource($category));
       } catch (ModelNotFoundException $e) {
        return response()->json(['message' => 'Category not found'], 404);
        }
    }

    public function store(CategoryRequest $request)
    {
        $data = $request->validated();
        $category = $this->categoryService->create($data);
        return response()->json(new CategoryResource($category), 201);
    }
    
    public function update(CategoryRequest $request, $id)
    {
        try {
            $category = $this->categoryService->update($id, $request->validated());
            return response()->json(new CategoryResource($category));
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Category not found'], 404);
        }
    }
    
    public function destroy($id)
    {
        try {
            $this->categoryService->delete($id);
            return response()->json(['message' => 'Category deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Category not found'], 404);
        }
    }

}
