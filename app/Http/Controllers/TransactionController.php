<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionCreateRequest;
use App\Http\Requests\TransactionUpdateRequest;
use App\Http\Resources\TransactionCollection;
use App\Http\Resources\TransactionGetResource;
use App\Http\Resources\TransactionResource;
use App\Models\Customer;
use App\Models\MasterItem;
use App\Models\StockItem;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{

    public function getTransaction($id): Transaction
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
                "errors" => "NOT_FOUND"
            ])->setStatusCode(404));
        }

        return $stockItem;
    }

    public function generateTrxNumber($itemId)
    {
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
    public function create(TransactionCreateRequest $request, $itemId, $customer_id): JsonResponse
    {
        $user = Auth::user();
        $data = $request->validated();

        $transaction = new Transaction($data);
        $transaction->trx_number = $this->generateTrxNumber($itemId);
        
        //get item
        $masterItem = MasterItem::find($itemId);

        $transaction->item_id = $itemId;
        $transaction->customer_id = $customer_id;
        $transaction->created_by = $user->id;
        
        // $stockItem = new StockItem();
        $stockItem = StockItem::where("item_id", $itemId)->orderByDesc("id")->first();
        
        DB::table("stock_items")->insert([
            "item_id" => $itemId,
            "stock" => $transaction->quantity * -1,
            "cogs" => $masterItem->cost_of_goods_sold,
            "selling_price" => $transaction->amount,
            "prev_stock_id" => $stockItem->id ?? 0,
            "created_by" => $user->id,
            "created_at" => Carbon::now(),
        ]);
        $transaction->save();

        return (new TransactionResource($transaction))->response()->setStatusCode(201);
    }

    public function getTodayTransaction(): TransactionCollection
    {
        $user = Auth::user();
        // $transaction = Transaction::with("customer.transaction")
        //                             ->select("trx_number", "description", "customers.customer_name")
        //                             ->whereDate('created_at', Carbon::today())
        //                             ->get();
        $transaction = DB::table("transactions")
            ->join("customers", "transactions.customer_id", 'customers.id')
            ->select("customers.customer_name", "customers.nik", "transactions.id",
                    "transactions.trx_number", "transactions.quantity", "transactions.amount",
                    "transactions.total", "transactions.description", "transactions.item_id",
                    "transactions.customer_id", "transactions.created_by", "transactions.created_at",)
            ->whereDate('transactions.created_at', Carbon::today())
            // ->orderByDesc("transactions.created_at")
            ->get();

        return new TransactionCollection($transaction);
    }

    public function update($id, TransactionUpdateRequest $request): TransactionResource
    {
        $user = Auth::user();
        $transaction = $this->getTransaction($id);
        $data = $request->validated();

        $transaction->fill($data);

        $transaction->updated_by = $user->id;
        $transaction->save();

        return new TransactionResource($transaction);
    }
}