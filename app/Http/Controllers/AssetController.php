<?php

namespace App\Http\Controllers;

use App\Http\Requests\Asset\AssetCreateRequest;
use App\Http\Resources\Asset\AssetCreateResource;
use App\Http\Resources\Asset\AssetGetDetailCollection;
use App\Http\Resources\Asset\AssetGetSummaryCollection;
use App\Services\AssetService;
use App\Services\MasterItemService;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{

    protected $service;
    protected $masterItemService;

    public function __construct(AssetService $service,
                                 MasterItemService $masterItemService) 
    {
        $this->service = $service;
        $this->masterItemService = $masterItemService;
    }

    public function create(AssetCreateRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        $asset = $this->service->create($data, $user);

        return (new AssetCreateResource($asset))->response()->setStatusCode(201);
    }

    public function update(AssetCreateRequest $request, $id)
    {
        $user = Auth::user();
        $data = $request->validated();

        $asset = $this->service->update($id, $data, $user);
        return new AssetCreateResource($asset);;
    }

    public function getSumAssetOwner()
    {
        Auth::user();
        $asset = $this->service->summaryAssetOwner();

        return new AssetGetSummaryCollection($asset);
    }

    public function getDetailAsset($ownerId, $item_id):AssetGetDetailCollection {

        Auth::user();

        $detailAsset = $this->service->getDetailAsset($ownerId, $item_id);

        return new AssetGetDetailCollection($detailAsset);
    }
}
