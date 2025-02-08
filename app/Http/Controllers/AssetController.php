<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssetCreateRequest;
use App\Http\Resources\AssetCreateResource;
use App\Models\Asset;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssetController extends Controller
{

    public function checkAssetNameExists(string $assetName)
    {
        if(Asset::where("name", $assetName)->count()==1){
            throw new HttpResponseException(response([
                "errors" => "ASSET_NAME_EXISTS"
            ], 400));
        }
    }

    public function querySumAssetOwner() {

        $sumAssetOwner = DB::table('assets')
                            ->join('asset_owners', 'asset_owners.id', 'assets.owner_id')
                            ->select('asset_owner.name', 'asset_name')
                            ->sum('quantity')->sum('cogs')->sum('selling_price')
                            ->groupBy
    }
    public function create(AssetCreateRequest $request): JsonResponse
    {
        $user = Auth::user();      
        $data = $request->validated();  
        $asset = new Asset($data);

        $this->checkAssetNameExists($data['asset_name']);

        $asset->created_by = $user->id;
        $asset->save();

        return (new AssetCreateResource($asset))->response()->setStatusCode(201);
    }
}
