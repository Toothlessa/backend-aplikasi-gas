<?php

namespace App\Services;

use App\Repositories\AssetRepository;
use App\Repositories\StockItemRepository;
use App\Repositories\MasterItemRepository;
use Illuminate\Http\Exceptions\HttpResponseException;

class AssetService
{
    protected $repository;
    protected $masterItemRepository;
    protected $stockItemRepository;

    public function __construct( AssetRepository $repository,
                                  MasterItemRepository $masterItemRepository,
                                  StockItemRepository $stockItemRepository) 
    {
        $this->repository = $repository;
        $this->masterItemRepository  = $masterItemRepository;
        $this->stockItemRepository = $stockItemRepository;
    }

    public function create($data, $user)
    {
        $masterItem = $this->masterItemRepository->findById($data['item_id']);

        // 3. Prepare stock item data
        $newData = array_merge($data, [
            'item_id'      => $masterItem->id,
            'cogs'         => $masterItem->cost_of_goods_sold,
            'selling_price'=> $masterItem->selling_price * $data['quantity'],
            'created_by'   => $user->id,
        ]);

        $asset = $this->repository->create($newData);
        return $asset;
    }

    public function update($id, $data, $user)
    {
        $asset = $this->repository->findById($id);

        $masterItem = $this->masterItemRepository->findById($data['item_id']);
        
         // 3. Prepare stock item data
        $newData = array_merge($data, [
            'item_id'      => $masterItem->id,
            'cogs'         => $masterItem->cost_of_goods_sold,
            'selling_price'=> $masterItem->selling_price,
            'updated_by' => $user->id,
        ]);

        $asset = $this->repository->update($asset,$newData);

        return $asset;
        
    }

    public function summaryAssetOwner()
    {
        $data = $this->repository->summaryAssetOwner();

        if(!$data) {
            throw new HttpResponseException(response()->json([
                "errors" => "SUMMARY_ASSET_OWNER_NOT_FOUND"
            ])->setStatusCode(404));
        }

        return $data;

    }

    public function getDetailAsset($ownerId, $itemId)
    {
        $data = $this->repository->getDetailAsset($ownerId, $itemId);

        if(!$data) {
            throw new HttpResponseException(response()->json([
                "errors" => "DETAIL_ASSET_NOT_FOUND"
            ])->setStatusCode(404));
        }

        return $data;
    }
}