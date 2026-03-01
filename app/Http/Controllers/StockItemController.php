<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockItem\StockItemCreateRequest;
use App\Http\Requests\StockItem\StockItemInputRequest;
use App\Http\Resources\StockItem\StockItemGetCollection;
use App\Http\Resources\StockItem\StockItemGetDetailCollection;
use App\Http\Resources\StockItem\StockItemResource;
use App\Http\Resources\StockItem\StockItemDisplayStockResource;
use App\Services\StockItemService;
use Illuminate\Support\Facades\Auth;

class StockItemController extends Controller
{
     protected $service;

    public function __construct(StockItemService $service)
    {
        $this->service = $service;
    }

    public function createNewStock($itemId, StockItemCreateRequest $request)
    {
        Auth::user();
        $data = $this->service->createNewStock(
                                        $itemId,
                                        $request->validated()['stock']);

        return new StockItemResource($data);
    }

    public function updateStock($id, StockItemInputRequest $request)
    {
        Auth::user();
        $stockData = $this->service->updateStock(
                                        $id,
                                        $request->validated());

        return new StockItemResource($stockData);
    }

    public function getCurrentStock(): StockItemGetCollection
    {
        Auth::user();
        $stock = $this->service->getCurrentStock();

        return new StockItemGetCollection($stock);
    }

    public function getDetailStock($itemId): StockItemGetDetailCollection {
        Auth::user();
        $stock = $this->service->getDetailStockByItem($itemId);

        return new StockItemGetDetailCollection($stock);
    }

    public function getDisplayStock($filledGasId, $emptyGasId)
    {
        Auth::user();

        $displayStock = $this->service->getDisplayStock($filledGasId, $emptyGasId);

        return (new StockItemDisplayStockResource($displayStock)->response()->setStatusCode(200));
    }

}
