<?php

namespace App\Http\Controllers;


use App\Http\Resources\MerchantResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Services\MerchantService;
use App\Http\Requests\MerchantRequest;
use Illuminate\Support\Facades\Auth;


class MerchantController extends Controller
{
    //
    private MerchantService $MerchantService;

    public function __construct(MerchantService $MerchantService)
    {
        $this->MerchantService = $MerchantService;
    }

    public function index()
    {
        $fields = ['id', 'name', 'photo', 'address', 'phone', 'keeper_id'];
        $categories = $this->MerchantService->getAll($fields ?: ['*']);
        return response()->json(MerchantResource::collection($categories));
    }

    public function show($id)
    {
        try {
            $fields = ['id', 'name', 'photo', 'address', 'phone', 'keeper_id'];
            $Merchant = $this->MerchantService->getById($id, $fields);
            return response()->json(new MerchantResource($Merchant));
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Merchant not found'], 404);
        }
    }

    public function store(MerchantRequest $request)
    {
        $data = $request->validated();
        $Merchant = $this->MerchantService->create($data);
        return response()->json(new MerchantResource($Merchant), 201);
    }

    public function update(MerchantRequest $request, $id)
    {
        try {
            $Merchant = $this->MerchantService->update($id, $request->validated());
            return response()->json(new MerchantResource($Merchant));
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Merchant not found'], 404);
        }
    }

    public function destroy($id)
    {
        try {
            $this->MerchantService->delete($id);
            return response()->json(['message' => 'Merchant deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Merchant not found'], 404);
        }
    }

    public function getMerchantProfile()
    {
        $UserId = Auth::id('1');
        try {
            $Merchant = $this->MerchantService->getByKeeperId($UserId);
            return response()->json(new MerchantResource($Merchant));
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Merchant not found'], 404);
        }
    }
}
