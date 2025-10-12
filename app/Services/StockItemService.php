<?php

namespace App\Services;

use App\Models\MasterItem;
use App\Repositories\AssetRepository;
use App\Repositories\StockItemRepository;
use App\Repositories\MasterItemRepository;
use Illuminate\Http\Exceptions\HttpResponseException;

class StockItemService
{
    protected $repository;
    protected $masterItemRepository;
    protected $assetRepository;

     public function __construct( StockItemRepository $repository,
                                  MasterItemRepository $masterItemRepository,
                                  AssetRepository $assetRepository) 
    {
        $this->repository = $repository;
        $this->masterItemRepository  = $masterItemRepository;
        $this->assetRepository = $assetRepository;
    }

    public function findById($id)
    {
        $stockItem = $this->repository->findById($id);

        if(!$stockItem){
            throw new HttpResponseException(response()->json([
                "errors" => "STOCK_NOT_FOUND"
            ])->setStatusCode(404));
        }

        return $stockItem;
    }

    public function create($itemId, $data, $user)
    {

        $masterItem = $this->masterItemRepository->findById($itemId);

         // 3. Prepare stock item data
        $data = array_merge($data, [
            'item_id'      => $masterItem->id,
            'cogs'         => $masterItem->cost_of_goods_sold,
            'selling_price'=> $masterItem->selling_price,
            'created_by'   => $user->id,
        ]);

         return $stockData = $this->repository->create($data);
    }

    public function update($id, $data, $user)
    {
        $stockData = $this->repository->findById($id);
        $data = array_merge($data, [
            'updated_by'    => $user->$id
        ]);

        return $this->repository->update($stockData, $data);
    }

    public function getCurrentStock()
    {
        $stockItem = $this->repository->getCurrentStock();

        if(!$stockItem) {
            throw new HttpResponseException(response()->json( [
                "errors" => "CURRENT_STOCK_NOT_FOUND"
            ])->setStatusCode(404));
        }

        return $stockItem;
    }

    public function getDetailStockByItem($itemId) 
    {
        $stockItem = $this->repository->getDetailStockByItem($itemId);

        if(!$stockItem) {
            throw new HttpResponseException(response()->json([
                "errors" => "DETAIL_STOCK_NOT_FOUND"
            ])->setStatusCode(404));
        }

        return $stockItem;
    }

    public function getDisplayStock($filledGasId, $emptyGasId)
    {
        $runningStock   = $this->repository->getStockByItemId($filledGasId); 
        $ownedGas       = $this->assetRepository->getSummaryAssetByItemId($emptyGasId);

        $yesterDayStock = $this->repository->getYesterdayStockByItemId($filledGasId);

        $emptyGas       = $ownedGas - $runningStock;

        $arrayName      = array('running_stock', 'yeterday_stock', 'empty_gas', 'gas_owned');
        $arrayValue     = array($runningStock ?? 0, $yesterDayStock ?? 0, $emptyGas ?? 0, $ownedGas ?? 0);
        
        return array_combine($arrayName, $arrayValue);
    }

}