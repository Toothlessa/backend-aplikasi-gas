<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockItemInputRequest;
use App\Http\Requests\StockUpdateRequest;
use App\Http\Requests\TransactionCreateRequest;
use App\Http\Requests\TransactionUpdateRequest;
use App\Http\Resources\TransactionCollection;
use App\Http\Resources\TransactionGetOutstandingCollection;
use App\Http\Resources\TransactionGetResource;
use App\Http\Resources\TransactionResource;
use App\Models\Customer;
use App\Models\MasterItem;
use App\Models\StockItem;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{

    public function getTransactionById($id): Transaction
    {
        $transaction = Transaction::find($id);
        if(!$transaction){
            throw new HttpResponseException(response()->json([
                "errors" => [
                   "NOT_FOUND"
                ]
            ])->setStatusCode(404));
        }

        return $transaction;
    }

    public function getStock($id): StockItem
    {
        $stockItem = StockItem::find($id);
        if(!$stockItem){
            throw new HttpResponseException(response()->json([
                "errors" => "STOCK_NOT_FOUND"
            ])->setStatusCode(404));
        }

        return $stockItem;
    }

    public function getTransactionByDate($date){
        $transaction = DB::table("transactions")
            ->join("customers", "transactions.customer_id", 'customers.id')
            ->join("stock_items", "transactions.stock_id", "stock_items.id")
            ->select("customers.customer_name", "customers.nik", "stock_id", "transactions.id",
                    "transactions.quantity", "transactions.amount",
                    "transactions.total", "transactions.description", "transactions.item_id",
                    "transactions.customer_id", "transactions.created_by", "transactions.created_at",)
            ->whereDate("transactions.created_at", $date)
            ->orderByDesc("transactions.created_at")
            ->get();

        if(!$transaction){
            throw new HttpResponseException(response()->json([
                "errors" => "NOT_FOUND"
            ])->setStatusCode(404));
        }

        return $transaction;
    }

    public function getDataSaleItem(){
        
        $transaction = DB::table('transactions')
            ->selectraw("DATE_FORMAT(created_at, '%Y-%m') AS month,
                                     DATE_FORMAT(created_at, '%d') AS day,  
                                     sum(quantity) as total")
            ->whereMonth("created_at", Carbon::now()->month)
            ->orderByDesc("created_at")
            ->groupBy("day")
            ->groupBy("month")
            ->limit(30)
            ->get();
        
        if(!$transaction){
            throw new HttpResponseException(response()->json([
                "errors" => "NOT_FOUND"
            ])->setStatusCode(404));
        }

        return $transaction;
    }

    public function getTopSalePerItem() {
        $transaction = DB::table('transactions')
            ->join("customers", "customers.id", "customer_id")
            ->selectraw("customer_id, customer_name, sum(quantity) as total")
            ->whereNotLike("customer_name", "%umum%")
            ->whereNotLike("customer_name", "%aulia%fauziah%")
            ->groupBy("customer_id", "customer_name")
            ->limit(7)
            ->get();

        if(!$transaction){
            throw new HttpResponseException(response()->json([
                "errors" => "NOT_FOUND"
            ])->setStatusCode(404));
        }
    
        return $transaction;
    }

    public function queryGetOutstandingTransaction() {
        $transaction = DB::table('transactions')
            ->join("customers", "customers.id", "customer_id")
            ->join("master_items", "master_items.id", "item_id")
            ->selectraw("transactions.id, stock_id, customers.id customer_id, customer_name, 
                                    item_name, description, quantity, amount, total, transactions.created_at")
            ->whereNotIn("description", ["umum", "balancing"])
            ->whereNotLike("description", "%done%")
            ->whereNotLike("description", "%teh iya%")
            ->orderBy("customer_id")
            ->orderBy("transactions.created_at")
            ->get();

        if(!$transaction){
            throw new HttpResponseException(response()->json([
                "errors" => "NOT_FOUND"
            ])->setStatusCode(404));
        }
    
        return $transaction;
    }

    public function generateTrxNumber($itemId) {
        $lastSeq = Transaction::where("item_id", $itemId)->orderByDesc("id")->first();

        if(isset($lastSeq['trx_number']))
        {   
            $seq = substr($lastSeq->trx_number, 6, 1);
            $trxNumber = "trx" . mt_rand(100,999) . (int)$seq + 1;
        } else {
            $trxNumber = "trx" . mt_rand(100, 999) . 1;
        }

        return $trxNumber;
    }

    public function create(StockItemInputRequest $stockRequest, TransactionCreateRequest $request): JsonResponse
    {
    
        $user = Auth::user();
        $dataStock = $stockRequest->validated();
        $dataTrx = $request->validated();

        return DB::transaction(function () use ($dataStock, $dataTrx, $user) {
            // Get item
            $masterItem = MasterItem::findOrFail($dataTrx['item_id']); // Use findOrFail to throw an exception if not found

            // Get the latest stock item
            $stockItem = StockItem::where("item_id", $dataTrx['item_id'])->orderByDesc("id")->first();

            // Create the new stock item
            $InputStockItem = new StockItem($dataStock);
            $InputStockItem->item_id = $dataTrx['item_id'];
            $InputStockItem->cogs = $masterItem->cost_of_goods_sold;
            $InputStockItem->selling_price = $masterItem->selling_price;
            $InputStockItem->prev_stock_id = $stockItem ? $stockItem->id : 0; // Use null coalescing operator
            $InputStockItem->created_by = $user->id;
            $InputStockItem->save();

            // Create the transaction
            $transaction = new Transaction($dataTrx);
            $transaction->item_id = $dataTrx['item_id'];
            $transaction->customer_id = $dataTrx['customer_id'];
            $transaction->stock_id = $InputStockItem->id;
            $transaction->created_by = $user->id;
            $transaction->save();

            return (new TransactionResource($transaction))->response()->setStatusCode(201);
        });
    }

    public function update($id, TransactionUpdateRequest $request, StockUpdateRequest $stockRequest,): TransactionResource
    {
        $user = Auth::user();
        $dataTrx = $request->validated();
        $dataStock = $stockRequest->validated();

        return DB::transaction(function () use ($id, $dataTrx, $dataStock, $user) {
            //update data
            $transaction = $this->getTransactionById($id);
            $transaction->fill($dataTrx);
            $transaction->updated_by = $user->id;
            $transaction->save();

            // update stock
            $stock = $this->getStock($dataStock['stock_id']);
            $stock->fill($dataStock);
            $stock->updated_by = $user->id;
            $stock->save();

            return new TransactionResource($transaction);
        });
    }

    public function getTransaction($date): TransactionCollection{

        $user = Auth::user();
        $transaction = $this->getTransactionByDate($date);

        return new TransactionCollection($transaction);
    }

    public function getOutstandingTransaction(): TransactionGetOutstandingCollection{

        $user = Auth::user();
        $transaction = $this->queryGetOutstandingTransaction();

        return new TransactionGetOutstandingCollection($transaction);
    }

   public function getSalesPerWeek(): JsonResponse {

    $user = Auth::user();
    $transaction = $this->getDataSaleItem();

    return response()->json($transaction);
   }

   public function getTopCustomer(): JsonResponse {

    $user = Auth::user();
    $transaction = $this->getTopSalePerItem();

    return response()->json($transaction);
   }

}