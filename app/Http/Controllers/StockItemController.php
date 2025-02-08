<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockItemInputRequest;
use App\Http\Resources\StockItemGetCollection;
use App\Http\Resources\StockItemGetDetailCollection;
use App\Http\Resources\StockItemResource;
use App\Models\MasterItem;
use App\Models\StockItem;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockItemController extends Controller
{
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

    public function queryGetCurrentStock() {

        $stock = DB::table("stock_items")
        ->join("master_items", "stock_items.item_id", 'master_items.id')
        ->selectRaw("stock_items.item_id, master_items.item_name, master_items.item_code, master_items.category, 
                 sum(stock) as total_stock, master_items.cost_of_goods_sold, master_items.selling_price")
        // ->whereRaw("master_items.id = COALESCE('$itemId', master_items.id)")
        ->groupBy("stock_items.item_id", "master_items.item_name","master_items.item_code", "master_items.category", 
                "master_items.cost_of_goods_sold", "master_items.selling_price")
        ->get();

        if(!$stock){
            throw new HttpResponseException(response()->json([
                "errors" => "NOT_FOUND"
            ])->setStatusCode(404));
        }

        return $stock;
    }

    public function queryGetDetailStock($itemId) {
        $stock = DB::table("stock_items")
                ->join("master_items", "stock_items.item_id", "master_items.id")
                ->selectRaw("stock_items.id, item_id, item_name, item_code, category, stock, stock_items.created_at")
                ->whereNull("prev_stock_id")
                ->where("item_id", $itemId)
                ->orderByDesc("stock_items.created_at")
                ->limit(3)
                ->get();

        if(!$stock){
            throw new HttpResponseException(response()->json([
                "errors" => "NOT_FOUND"
            ])->setStatusCode(404));
        }

        return $stock;
    }

    public function create($itemId, StockItemInputRequest $request): JsonResponse
    {
        $user = Auth::user();
        $masterItem = MasterItem::find($itemId);
        $data = $request->validated();

        $InputStockItem = new StockItem($data);

        $InputStockItem->item_id = $masterItem->id;
        $InputStockItem->cogs = $masterItem->cost_of_goods_sold;
        $InputStockItem->selling_price = $masterItem->selling_price;
        $InputStockItem->created_by = $user->id;
        $InputStockItem->save();

        return (new StockItemResource($InputStockItem))->response()->setStatusCode(201);
    }

    public function update($id, StockItemInputRequest $request): StockItemResource
    {
        $user = Auth::user();
        $inputStock = $this->getStock($id);
        $data = $request->validated();

        $inputStock->fill($data);

        $inputStock->updated_by = $user->id;
        $inputStock->save();

        return new StockItemResource($inputStock);
    }

    public function getCurrentStock(): StockItemGetCollection
    {
        $user = Auth::user();
        $stock = $this->queryGetCurrentStock();

        return new StockItemGetCollection($stock);
    }

    public function getDetailStock($itemId): StockItemGetDetailCollection {
        $user = Auth::user();
        $stock = $this->queryGetDetailStock($itemId);

        return new StockItemGetDetailCollection($stock);
    }
}
