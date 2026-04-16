<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Services\TransactionService;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class TransactionController extends Controller
{
    private TransactionService $transactionService;
    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }


    public function index()
    {
        $fields = ['*'];
        $transaction = $this->transactionService->getAll($fields);
        return response()->json(TransactionResource::collection($transaction));
    }

    public function store(TransactionRequest $request)
    {
        $transaction = $this->transactionService->createTransaction($request->validated());
        return response()->json([
            'message' => 'Transaction Successfully Recorded',
            'data' => $transaction
        ], 201);
    }

    public function show(int $id)
    {
        try {
            $fields = ['*'];
            $transaction = $this->transactionService->getByTransactionId($id, $fields);
            return response()->json(new TransactionResource($transaction));
        } catch (ModelNotFoundException $e) {

            return response()->json([
                'message' => 'transaction not found'
            ], 404);
        }
    }

    public function getTransactionByMerchant()
    {
        $user = auth()->user();
        if (!$user || !$user->merchant) {
            return response()->json(['message' => 'No merchant assigned'], 403);
        }

        $merchantId = $user->merchant->id;
        $transaction = $this->transactionService->getTransactionByMerchant($merchantId);
        return response()->json($transaction);
    }
}
