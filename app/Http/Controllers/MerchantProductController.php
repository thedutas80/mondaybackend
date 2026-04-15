<?php

namespace App\Http\Controllers;

use App\Http\Requests\MerchantProductRequest;
use App\Http\Requests\MerchantProductUpdateRequest;
use App\Services\MerchantProductService;


class MerchantProductController extends Controller
{
    //
    private MerchantProductService $MerchantProductService;

    public function __construct(MerchantProductService $MerchantProductService)
    {
        $this->MerchantProductService = $MerchantProductService;
        
    }

    public function store(MerchantProductRequest $request,int $merchant)

    {
       $validated = $request->validated();
       $validated['merchant_id'] = $merchant;
    
        $merchantProduct = $this->MerchantProductService->assignProductToMerchant($validated);
        return response()->json
        ([
            'message' => 'Product assigned to merchant successfully',
            'data' => $merchantProduct
        ], 201);
    }

    public function update(MerchantProductUpdateRequest $request, int $merchant, int $product)
    {
        $validated = $request->validated();

        $merchantProduct = $this->MerchantProductService->UpdateStock($merchant, $product, $validated['stock'], $validated['warehouse_id'] ?? null);
        return response()->json([
            'message' => 'Merchant product updated successfully',
            'data' => $merchantProduct
        ]);

    }

    public function destroy(int $merchant, int $product)
    {
        $this->MerchantProductService->removeProductFromMerchant($merchant, $product);
        return response()->json([
            'message' => 'Product removed from merchant successfully'
        ]);
    }
}