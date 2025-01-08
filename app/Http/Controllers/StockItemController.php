<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockItemInputRequest;
use App\Http\Resources\StockItemResource;
use App\Models\MasterItem;
use App\Models\StockItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class StockItemController extends Controller
{
    public function inputStock($itemId, StockItemInputRequest $request): JsonResponse
    {
        $user = Auth::user();
        $masterItem = MasterItem::find($itemId)->first();
        $data = $request->validated();

        $InputStockItem = new StockItem($data);
        $InputStockItem->item_id = $masterItem->id;
        $InputStockItem->cogs = $masterItem->cost_of_goods_sold;
        $InputStockItem->selling_price = $masterItem->selling_price;
        $InputStockItem->created_by = $user->id;
        $InputStockItem->save();

        return (new StockItemResource($InputStockItem))->response()->setStatusCode(201);
    }
}
