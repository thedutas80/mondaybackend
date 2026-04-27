<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\WarehouseProductController;
use App\Http\Controllers\MerchantProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRoleController;
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



//Route::post('token-login', [AuthController::class, 'tokenLogin']); // untuk testing di Postman
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);



Route::middleware('auth:sanctum')->group(function () {
    Route::get('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user']);
});



// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');



Route::middleware(['auth:sanctum', 'role:manager'])->group(function () {

    Route::apiResource('transactions', TransactionController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('roles', RoleController::class);
    Route::post('users/roles', [UserRoleController::class, 'assignrole']);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('warehouses', WarehouseController::class);
    Route::apiResource('merchants', MerchantController::class);
    Route::post('warehouses/{warehouse}/products', [WarehouseProductController::class, 'attach']);
    Route::delete('warehouses/{warehouse}/products/{product}', [WarehouseProductController::class, 'detach']);
    Route::put('warehouses/{warehouse}/products/{product}', [WarehouseProductController::class, 'update']);
    Route::get('warehouses/{warehouse}/products', [WarehouseProductController::class, 'index']);
    Route::post('merchants/{merchant}/products', [MerchantProductController::class, 'store']);
    Route::delete('merchants/{merchant}/products/{product}', [MerchantProductController::class, 'detach']);
    Route::put('merchants/{merchant}/products/{product}', [MerchantProductController::class, 'update']);
});



Route::middleware(['auth:sanctum', 'role:manager|keeper'])->group(function () {

    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{category}', [CategoryController::class, 'show']);

    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{product}', [ProductController::class, 'show']);

    Route::get('warehouses', [WarehouseController::class, 'index']);
    Route::get('warehouses/{warehouse}', [WarehouseController::class, 'show']);


    Route::post('transaction', [TransactionController::class, 'store']);
    Route::get('transaction/{transaction}', [TransactionController::class, 'show']);
    Route::get('my-merchant', [MerchantController::class, 'getMerchantProfile']);
    Route::get('/my-merchant/transactions', [TransactionController::class, 'getTransactionByMerchant']);
});
