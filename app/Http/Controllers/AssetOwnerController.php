<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssetOwner\AssetOwnerCreateRequest;
use App\Http\Requests\AssetOwner\AssetOwnerUpdateRequest;
use App\Http\Resources\AssetOwner\AssetOwnerCreateResource;
use App\Http\Resources\AssetOwner\AssetOwnerCollection;
use App\Http\Resources\AssetOwner\AssetOwnerResource;
use App\Http\Resources\AssetOwner\AssetOwnerUpdateResource;
use App\Services\AssetOwnerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AssetOwnerController extends Controller
{
    protected $service;

    public function __construct(AssetOwnerService $service)
    {
        $this->service = $service;
    }

    public function create(AssetOwnerCreateRequest $request): JsonResponse {

        $user = Auth::user();
        $data=$request->validated();

        $assetOwner = $this->service->create($data, $user);

        return (new AssetOwnerCreateResource($assetOwner))->response()->setStatusCode(201);
    }

    public function update($id, AssetOwnerUpdateRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        $assetOwner = $this->service->update($id, $data, $user);

        return (new AssetOwnerUpdateResource($assetOwner))->response()->setStatusCode(200);
    }

    public function find($id)
    {
        Auth::user();
        $assetOwner = $this->service->findById($id);

        return (new AssetOwnerResource($assetOwner))->response()->setStatusCode(200);
    }

    public function getAll()
    {
        Auth::user();
        $assetOwner = $this->service->getAll();

        return (new AssetOwnerCollection($assetOwner))->response()->setStatusCode(200);
    }

    public function inactiveOwner($id) 
    {
        $user = Auth::user();
        $assetOwner = $this->service->inactiveOwner($id, $user);

        return (new AssetOwnerResource($assetOwner))->response()->setStatusCode(200);
    }
}
