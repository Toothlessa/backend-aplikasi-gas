<?php

namespace App\Http\Controllers;

use App\Http\Requests\MasterItem\MasterItemCreateRequest;
use App\Http\Requests\MasterItem\MasterItemUpdateRequest;
use App\Http\Resources\MasterItem\MasterItemCollection;
use App\Http\Resources\MasterItem\MasterItemResource;
use App\Services\MasterItemService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MasterItemController extends Controller
{
    protected $service;

    public function __construct(MasterItemService $service)
    {
        $this->service = $service;
    }

    public function create(MasterItemCreateRequest $request): JsonResponse
    {
        $user = Auth::user();
        $data = $request->validated();

        $masterItem = $this->service->create($data, $user);

        return (new MasterItemResource($masterItem))->response()->setStatusCode(201);
    }

    public function update($id, MasterItemUpdateRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        $masterItem = $this->service->update($id, $data, $user);

        return (new MasterItemResource($masterItem))->response()->setStatusCode(200);
    }

    public function findById($id)
    {
        Auth::user();
        $masterItem = $this->service->findById($id);

       return (new MasterItemResource($masterItem))->response()->setStatusCode(200);
    }

    public function getAll()
    {
        Auth::user();

        $masterItem = $this->service->getAll();
        return (new MasterItemCollection($masterItem))->response()->setStatusCode(200);
    }

    public function getItemByItemType($itemType)
    {
        Auth::user();

        $masterItem = $this->service->getItemByItemType($itemType);

         return (new MasterItemCollection($masterItem))->response()->setStatusCode(200);
    }

    public function getItemByFlagStatus($flagStatus)
    {
        Auth::user();

        $masterItem = $this->service->getItemByFlagStatus($flagStatus);

        return (new MasterItemCollection($masterItem))->response()->setStatusCode(200);
    }

    public function inactiveItem($id): MasterItemResource {
        $user = Auth::user();
        
        $masterItem = $this->service->inactiveItem($id, $user);
    
        return new MasterItemResource($masterItem);
    }
}
