<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssetOwnerCreateRequest;
use App\Http\Requests\CustomerCreateRequest;
use App\Http\Resources\AssetOwnerCreateResource;
use App\Http\Resources\AssetOwnerGetAllCollection;
use App\Models\AssetOwner;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AssetOwnerController extends Controller
{
    public function getAssetOwnerById(int $id): AssetOwner
    {
        $assetOwner = AssetOwner::where('id', $id)->first();
        if(!$assetOwner){
            throw new HttpResponseException(response()->json([
                'errors' => "NOT_FOUND"
            ])->setStatusCode(404));
        }

        return $assetOwner;
    }
    public function checkNameExists(string $name)
    {
        if(AssetOwner::where("name", $name)->count() == 1){
            throw new HttpResponseException(response([
                "errors" => "NAME_EXISTS"
            ], 400));
        }
    }
    public function create(AssetOwnerCreateRequest $request): JsonResponse {

        $user = Auth::user();
        $data=$request->validated();

        if(isset($data['name'])) {
            $this->checkNameExists($data["name"]);
        }
        
        $assetOwner = new AssetOwner($data);
        $assetOwner->created_by = $user->id;
        $assetOwner->save();

        return (new AssetOwnerCreateResource($assetOwner))->response()->setStatusCode(201);
    }

    public function get($id): AssetOwnerCreateResource
    {
        $user = Auth::user();
        $assetOwner = $this->getAssetOwnerById($id);

        return new AssetOwnerCreateResource($assetOwner);
    }

    public function getAll(): AssetOwnerGetAllCollection
    {
        $user = Auth::user();
        $assetOwner = AssetOwner::query()
                                ->orderByDesc('active_flag')
                                ->orderBy('name')
                                ->get();

        return new AssetOwnerGetAllCollection($assetOwner);
    }

    public function update($id, AssetOwnerCreateRequest $request): AssetOwnerCreateResource
    {
        $user = Auth::user();
        $assetOwner = $this->getAssetOwnerById($id);
        $data = $request->validated();

        $assetOwner->fill($data);

        if(isset($data['name'])) {
            $this->checkNameExists($data["name"]);
        }

        $assetOwner->updated_by = $user->id;
        $assetOwner->save();

        return new AssetOwnerCreateResource($assetOwner);
    }

    public function delete($id): JsonResponse
    {
        $user = Auth::user();
        $assetOwner = $this->getAssetOwnerById($id);
        $assetOwner->delete();

        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }

    public function inactiveOwner($id): AssetOwnerCreateResource {
        $user = Auth::user();
        $assetOwner = $this->getAssetOwnerById($id);

        if($assetOwner->active_flag == 'Y') {
            $assetOwner->active_flag = 'N';
        } else {
            $assetOwner->active_flag = 'Y';
        }
        $assetOwner->inactive_date = Carbon::now();
        $assetOwner->updated_by = $user->id;
        $assetOwner->save();

        return new AssetOwnerCreateResource($assetOwner);
    }
}
