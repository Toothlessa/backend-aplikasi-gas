<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssetCreateRequest;
use App\Http\Resources\AssetCreateResource;
use App\Http\Resources\AssetGetDetailCollection;
use App\Http\Resources\AssetGetSummaryCollection;
use App\Models\Asset;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssetController extends Controller
{

    public function checkAssetNameExists(string $assetName)
    {
        if(Asset::where("asset_name", $assetName)->count()==1){
            throw new HttpResponseException(response([
                "errors" => "ASSET_NAME_EXISTS"
            ], 400));
        }
    }

    public function querySumAssetOwner() {

        $sumAssetOwner = DB::table('assets')
                            ->join('asset_owners', 'asset_owners.id', 'assets.owner_id')
                            ->selectRaw('owner_id, asset_owners.name, asset_name, 
                                        SUM(quantity) AS quantity, SUM(cogs) AS cogs, SUM(selling_price) AS selling_price')
                            ->groupByRaw('owner_id, asset_owners.name, asset_name')
                            ->get();
        
        if(!$sumAssetOwner) {
            throw new HttpResponseException(response([
                "errors" => "NOT_FOUND"
            ], 404));
        }

        return $sumAssetOwner;
    }

    public function queryDetailAsset($ownerId) {

        $detailAsset = DB::table('assets')
                        ->join('asset_owners', 'asset_owners.id', 'assets.owner_id')
                        -> select('assets.id', 'owner_id', 'asset_owners.name', 'asset_name', 
                                   'description', 'quantity', 'cogs', 'selling_price', 'assets.created_at')
                        ->where('owner_id', $ownerId)
                        ->orderBy('assets.created_at')
                        ->get();

        if(!$detailAsset) {
            throw new HttpResponseException(response([
                "errors" => "NOT_FOUND"
            ], 404));
        }

        return $detailAsset;
    }

    public function create(AssetCreateRequest $request): JsonResponse
    {
        $user = Auth::user();      
        $data = $request->validated();  
        $asset = new Asset($data);

        $asset->cogs = $data['cogs'] * $data['quantity'];
        $asset->selling_price = $data['selling_price'] * $data['quantity'];
        $asset->created_by = $user->id;
        $asset->save();

        return (new AssetCreateResource($asset))->response()->setStatusCode(201);
    }

    public function getAssetById($id):Asset {
        $user = Auth::user();

        $asset = Asset::find($id);

        return $asset;
    }
    public function getSumAssetOwner():AssetGetSummaryCollection {

        $user = Auth::user();

        $sumAssetOwner = $this->querySumAssetOwner();

        return new AssetGetSummaryCollection($sumAssetOwner);
    }

    public function getDetailAsset($ownerId):AssetGetDetailCollection {

        $user = Auth::user();

        $detailAsset = $this->queryDetailAsset($ownerId);

        return new AssetGetDetailCollection($detailAsset);
    }

    public function update(AssetCreateRequest $request, $id): AssetCreateResource {

        $user = Auth::user();
        
        $data = $request->validated();

        if(isset($data['asset_name'])) {
            $this->checkAssetNameExists($data['asset_name']);
        }

        $asset = $this->getAssetById($id);
        $asset->fill($data);

        $asset->cogs = $data['cogs'] * $data['quantity'];
        $asset->selling_price = $data['selling_price'] * $data['quantity'];
        $asset->updated_by = $user->id;
        $asset->updated_at = Carbon::now();
        $asset->save();

        return new AssetCreateResource($asset);
    }
}
