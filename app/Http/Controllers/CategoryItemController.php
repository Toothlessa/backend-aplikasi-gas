<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryItem\CategoryItemCreateRequest;
use App\Http\Resources\CategoryItem\CategoryItemCreateResource;
use App\Http\Resources\CategoryItem\CategoryItemGetAllCollection;
use App\Services\CategoryItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CategoryItemController extends Controller
{
    protected $service;
    
    public function __construct(CategoryItemService $service)
    {
        $this->service = $service;
    }

    public function create(CategoryItemCreateRequest $request): JsonResponse {

        $user = Auth::user();
        $data=$request->validated();
        
        $categoryItem = $this->service->create($data, $user);

        return (new CategoryItemCreateResource($categoryItem))->response()->setStatusCode(201);
    }

    public function update($id, CategoryItemCreateRequest $request): CategoryItemCreateResource
    {
        $user = Auth::user();
        $data = $request->validated();
        
        $categoryItem = $this->service->update($id,$data, $user);

        return new CategoryItemCreateResource($categoryItem);
    }

    public function delete($id): JsonResponse //this function is inactive
    {
        Auth::user();

        return $this->service->delete($id);
    }

    public function get($id): CategoryItemCreateResource
    {
        Auth::user();
        $assetOwner = $this->service->getCategoryItemId($id);

        return new CategoryItemCreateResource($assetOwner);
    }

    public function getAll(): CategoryItemGetAllCollection
    {
        Auth::user();
        $assetOwner = $this->service->getAllCategoryItem();

        return new CategoryItemGetAllCollection($assetOwner);
    }

    public function getActiveCategoryItems(): CategoryItemGetAllCollection
    {
        Auth::user();
        $assetOwner = $this->service->getActiveCategoryItem();

        return new CategoryItemGetAllCollection($assetOwner);
    }

    public function inactiveOwner($id): CategoryItemCreateResource {
        $user = Auth::user();
        $categoryItem = $this->service->inactiveCategoryItem($id, $user);

        return new CategoryItemCreateResource($categoryItem);
    }
}
