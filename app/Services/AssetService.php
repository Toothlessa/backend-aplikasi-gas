<?php

namespace App\Services;

use App\Repositories\AssetRepository;
use App\Repositories\StockItemRepository;
use App\Repositories\MasterItemRepository;
use Illuminate\Http\Exceptions\HttpResponseException;

class AssetService
{
    protected $repository;
    protected $assetOwnerService;
    protected $masterItemService;

    public function __construct(    AssetRepository $repository, 
                                    AssetOwnerService $assetOwnerService,
                                    MasterItemService $masterItemService
                                 ) 
    {
        $this->repository = $repository;
        $this->assetOwnerService = $assetOwnerService;
        $this->masterItemService  = $masterItemService;
    }

    public function create($data, $user) {
        #validate data
        $assetOwner = $this->assetOwnerService->findById($data["owner_id"]);
        $masterItem = $this->masterItemService->findById($data['item_id']);

        $asset = [
            # Frontend Input
            'quantity'          => $data['quantity'],
            'cogs'              => $data['cogs'],
            'selling_price'     => $data['selling_price'],
            'description'       => $data['description'],
            # Backend Process and Validate
            'owner_id'          => $assetOwner->id,
            'item_id'           => $masterItem->id,
            'created_by'        => $user->id,
        ];

        return $this->repository->create($asset);
    }

    public function update($id, $data, $user)
    {
        # Validate Data
        $asset      = $this->findById($id);
        $assetOwner = $this->assetOwnerService->findById($data["owner_id"]);
        $masterItem = $this->masterItemService->findById($data['item_id']);
        
        $newAsset = [
            # Frontend Input
            'quantity'          => $data['quantity'],
            'cogs'              => $data['cogs'],
            'selling_price'     => $data['selling_price'],
            'description'       => $data['description'],
            # Backend Process and Validate
            'owner_id'          => $assetOwner->id,
            'item_id'       => $masterItem->id,
            'updated_by'    => $user->id,
        ];

        return $this->repository->update($asset, $newAsset);
    }

    public function findById($id)
    {
        $asset = $this->repository->findById($id);

        if(!$asset) {
            throw new HttpResponseException(response()->json([
                "error" => "ASSET_NOT_FOUND"
            ])->setStatusCode(404));
        }

        return $asset;
    }

    public function getSummaryAssetByOwner()
    {
        $summaryAssetByOwner = $this->repository->getSummaryAssetByOwner();

        if(!$summaryAssetByOwner) {
            throw new HttpResponseException(response()->json([
                "error" => "SUMMARY_ASSET_OWNER_NOT_FOUND"
            ])->setStatusCode(404));
        }

        return $summaryAssetByOwner;

    }

    public function getDetailAsset($ownerId, $itemId)
    {
        $detailAsset = $this->repository->getDetailAsset($ownerId, $itemId);

        if(!$detailAsset) {
            throw new HttpResponseException(response()->json([
                "error" => "DETAIL_ASSET_NOT_FOUND"
            ])->setStatusCode(404));
        }

        return $detailAsset;
    }

    public function getSummaryAssetByItemId($itemId)
    {
        $summaryAssetByItemId = $this->repository->getSummaryAssetByItemId($itemId);

        if(!$summaryAssetByItemId) {
            throw new HttpResponseException(response()->json([
                "error" => "SUMMARY_ASSET_NOT_FOUND"
            ])->setStatusCode(404));
        }

        return $summaryAssetByItemId;
    }
}