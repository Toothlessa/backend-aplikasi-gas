<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockItem\StockItemInputRequest;
use App\Http\Requests\StockItem\StockUpdateRequest;
use App\Http\Requests\Transaction\TransactionCreateRequest;
use App\Http\Requests\Transaction\TransactionUpdateRequest;
use App\Http\Resources\Transaction\TransactionCollection;
use App\Http\Resources\Transaction\TransactionDailyCollection;
use App\Http\Resources\Transaction\TransactionOutstandingCollection;
use App\Http\Resources\Transaction\TransactionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Services\TransactionService;

class TransactionController extends Controller
{
     protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

     public function create(StockItemInputRequest $stockRequest,
                            TransactionCreateRequest $request)
    {
        $user = Auth::user();
        $dataStock = $stockRequest->validated();
        $dataTrx   = $request->validated();

        $transaction = $this->transactionService->create($dataStock, $dataTrx, $user);

        return (new TransactionResource($transaction))
            ->response()
            ->setStatusCode(201);
    }

    public function update($id, TransactionUpdateRequest $request, StockUpdateRequest $stockRequest) {

        $user = Auth::user();
        $dataTrx   = $request->validated();
        $dataStock = $stockRequest->validated();

        $transaction = $this->transactionService->update($id, $dataTrx, $dataStock, $user);

        return (new TransactionResource($transaction))->response()->setStatusCode(200);
    }

    public function getTransactionByDate($date)
    {
        Auth::user();
        $transaction = $this->transactionService->getTransactionByDate($date);

        return (new TransactionCollection($transaction))->response()->setStatusCode(200);
    }

    public function getOutstandingTransaction(){

        Auth::user();
        $transaction = $this->transactionService->getOutsTransaction();

        return (new TransactionOutstandingCollection($transaction))->response()->setStatusCode(200);
    }

   public function getDailySale() {

        Auth::user();
        $transaction = $this->transactionService->getDailySale();

        return (new TransactionDailyCollection($transaction))->response()->setStatusCode(200);
   }

   public function getTopCustomer(): JsonResponse {

        Auth::user();
        $transaction = $this->transactionService->getTopCustomer();

        return response()->json($transaction);
   }

}