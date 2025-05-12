<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryItemCreateRequest;
use App\Http\Resources\CategoryItemCreateResource;
use App\Http\Resources\CategoryItemGetAllCollection;
use App\Models\CategoryItem;
use App\Models\MasterItem;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryItemController extends Controller
{
    public function getCategoryItemById(int $id): CategoryItem
    {
        $categoryItem = CategoryItem::find($id);
        if(!$categoryItem){
            throw new HttpResponseException(response()->json([
                'errors' => "NOT_FOUND"
            ])->setStatusCode(404));
        }

        return $categoryItem;
    }

    public function checkNameExists(string $name)
    {
        if(CategoryItem::where("name", $name)->count() == 1){
            throw new HttpResponseException(response([
                "errors" => "NAME_EXISTS"
            ], 400));
        }
    }

    public function create(CategoryItemCreateRequest $request): JsonResponse {

        $user = Auth::user();
        $data=$request->validated();

        if(isset($data['name'])) {
            $this->checkNameExists($data["name"]);
        }
        
        $categoryItem = new CategoryItem($data);
        $categoryItem->created_by = $user->id;
        $categoryItem->save();

        return (new CategoryItemCreateResource($categoryItem))->response()->setStatusCode(201);
    }

    public function get($id): CategoryItemCreateResource
    {
        $user = Auth::user();
        $assetOwner = $this->getCategoryItemById($id);

        return new CategoryItemCreateResource($assetOwner);
    }

    public function getAll(): CategoryItemGetAllCollection
    {
        $user = Auth::user();
        $assetOwner = CategoryItem::query()
                                ->orderByDesc('active_flag')
                                ->orderBy('name')
                                ->get();

        return new CategoryItemGetAllCollection($assetOwner);
    }

    public function getActiveCategoryItems(): CategoryItemGetAllCollection
    {
        $user = Auth::user();
        $assetOwner = CategoryItem::query()
                                ->where('active_flag', 'Y')
                                ->orderBy('name')
                                ->get();

        return new CategoryItemGetAllCollection($assetOwner);
    }

    public function update($id, CategoryItemCreateRequest $request): CategoryItemCreateResource
    {
        $user = Auth::user();
        $data = $request->validated();
        
        $categoryItem = $this->getCategoryItemById($id);
        $categoryItem->fill($data);

        if($data['name'] != $categoryItem->name) {
            $this->checkNameExists($data['name']);
        }

        $categoryItem->updated_by = $user->id;
        $categoryItem->save();

        return new CategoryItemCreateResource($categoryItem);
    }

    public function delete($id): JsonResponse
    {
        $user = Auth::user();
        $assetOwner = $this->getCategoryItemById($id);

        $categoryInTrx = MasterItem::where('category_id, $id');

        if($categoryInTrx) {
            throw new HttpResponseException(response([
                "errors" => "THIS_CATEGORY_EXISTS_IN_TRANSACTION"
            ], 400));
        }

        $assetOwner->delete();

        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }

    public function inactiveOwner($id): CategoryItemCreateResource {
        $user = Auth::user();
        $categoryItem = $this->getCategoryItemById($id);

        if($categoryItem->active_flag == 'Y') {
            $categoryItem->active_flag = 'N';
        } else {
            $categoryItem->active_flag = 'Y';
        }
        $categoryItem->inactive_date = Carbon::now();
        $categoryItem->updated_by = $user->id;
        $categoryItem->save();

        return new CategoryItemCreateResource($categoryItem);
    }
}
