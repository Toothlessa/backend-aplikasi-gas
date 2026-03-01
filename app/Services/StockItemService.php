<?php

namespace App\Services;

use App\Repositories\StockItemRepository;
use Illuminate\Http\Exceptions\HttpResponseException;

class StockItemService
{
    protected $repository;
    protected $masterItemService;
    protected $assetService;

    public function __construct(StockItemRepository $repository,
                                MasterItemService $masterItemService,
                                AssetService $assetService)
    {
        $this->repository           = $repository;
        $this->masterItemService    = $masterItemService;
        $this->assetService         = $assetService;
    }

    public function createNewStock($itemId, $newStockValue)
    {
        $masterItem = $this->masterItemService->findById($itemId);

        $stockData = [
            'item_id'       => $masterItem->id,
            'stock'         => $newStockValue,
            'cogs'          => $masterItem->cost_of_goods_sold,
            'selling_price' => $masterItem->selling_price,
            'prev_stock_id' => 0, # 0 when initial stock
        ];

        return $this->repository->create($stockData);
    }

    public function autoStockFromTransaction($itemId, 
                                             $qtyStock, 
                                             $sellingPrice,
                                             $cogs) {

        $lastStock = $this->findLatestStock($itemId);

        if(!$lastStock) {
            $lastStock = 0;
        }

        $stockData = [
            'item_id'       => $itemId,
            'stock'         => $qtyStock,
            'cogs'          => $cogs,
            'selling_price' => $sellingPrice,
            'prev_stock_id' => $lastStock->id,
        ];

        return $this->repository->create($stockData);
    }

    public function updateStock($id, $data)
    {
        # validate data existence
        $this->masterItemService->findById($data['item_id']);

        $stockData = $this->findById($id);
        
        return $this->repository->update($stockData, $data);
    }

    public function findById($id)
    {
        $stockItem = $this->repository->findByIdOrFail($id);

        if (! $stockItem) {
            throw new HttpResponseException(response()->json([
                'errors' => 'STOCK_NOT_FOUND',
            ])->setStatusCode(404));
        }

        return $stockItem;
    }

    public function findLatestStock(int $itemId)
    {
        $stockItem = $this->repository->findLatestStock($itemId);

        if(!$stockItem) {
            return 0;
        }

        return $stockItem;
    }

    public function getStockByItemId(int $itemId)
    {
        $stockItem = $this->repository->getStockByItemId($itemId);

        if(!$stockItem) {
            return 0;
        }

        return $stockItem;
    }

    public function getStockNotToday(int $itemId)
    {
        $stockItem = $this->repository->getStockNotToday($itemId);

        if(!$stockItem) {
            return 0;
        }

        return $stockItem;
    }

    public function getCurrentStock()
    {
        $stockItem = $this->repository->getCurrentStock();

        if (! $stockItem) {
            throw new HttpResponseException(response()->json([
                'error' => 'CURRENT_STOCK_NOT_FOUND',
            ])->setStatusCode(404));
        }

        return $stockItem;
    }

    public function getDetailStockByItem($itemId)
    {
        $stockItem = $this->repository->getDetailStockByItem($itemId);

        if ($stockItem->isEmpty()) {
            throw new HttpResponseException(response()->json([
                'error' => 'DETAIL_STOCK_NOT_FOUND',
            ])->setStatusCode(404));
        }

        return $stockItem;
    }

    public function getDisplayStock(int $filledGasId, int $emptyGasId): array{
        // Current running stock (gas terisi yang sedang beredar)
        $runningStock = (int) $this->getStockByItemId($filledGasId);

        // Total gas owned (aset tabung kosong)
        $ownedGas = (int) $this->assetService->getSummaryAssetByItemId($emptyGasId);

        // Stock snapshot sebelum hari ini
        $yesterdayStock = (int) $this->getStockNotToday($filledGasId);

        // Gas kosong = total tabung - yang sedang terisi
        $emptyGas = max($ownedGas - $runningStock, 0);

        return [
            'running_stock'   => $runningStock,
            'yesterday_stock' => $yesterdayStock,
            'empty_gas'       => $emptyGas,
            'gas_owned'       => $ownedGas,
        ];
    }
}
