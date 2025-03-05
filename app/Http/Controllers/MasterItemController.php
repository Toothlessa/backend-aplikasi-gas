<?php

namespace App\Http\Controllers;

use App\Http\Requests\MasterItemCreateRequest;
use App\Http\Requests\MasterItemUpdateRequest;
use App\Http\Resources\MasterItemCollection;
use App\Http\Resources\MasterItemResource;
use App\Models\CategoryItem;
use App\Models\MasterItem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterItemController extends Controller
{
    public function getItem($id): MasterItem
    {
        $masterItem = MasterItem::find($id);
        if(!$masterItem){
            throw new HttpResponseException(response()->json([
                "errors" => "NOT_FOUND"
            ])->setStatusCode(404));
        }

        return $masterItem;
    }

    public function checkItemExists(string $itemName)
    {
        if(MasterItem::where("item_name", $itemName)->count()==1){
            throw new HttpResponseException(response([
                "errors"=> "ITEM_NAME_IS_REGISTERED"
            ], 400));
        }
    } 

    public function checkItemCodeExists(string $itemCode)
    {
        if(MasterItem::where("item_code", $itemCode)->count()==1){
            throw new HttpResponseException(response([
                "errors"=> "ITEM_CODE_IS_REGISTERED"
            ], 400));
        }
    } 
    public function generateItemCodeSeq(string $categoryId): string
    {
        $lastSeq = MasterItem::where("category_id", $categoryId)->orderByDesc("id")->first();
        $categoryItem = CategoryItem::find($categoryId);

        if($lastSeq) {
            $getPrefix = substr($lastSeq->item_code, 0, 2);
            $getSubfix = sprintf('%02d', (int)substr($lastSeq->item_code, 2, 2) + 1);
            
        $itemCode = $getPrefix . $getSubfix;

        } elseif ($categoryItem->name == "Bahan Pokok"){
            
            $itemCode = "BP01";
        }

        return $itemCode;
    }

    public function create(MasterItemCreateRequest $request): JsonResponse
    {
        $user = Auth::user();
        $data = $request->validated();

        $this->checkItemExists($data['item_name']);
        $getItemCode = $this->generateItemCodeSeq($data['category_id']);

        $masterItem = new MasterItem($data);
        $masterItem->item_code = $getItemCode;
        $masterItem->created_by = $user->id;
        $masterItem->save();

        return (new MasterItemResource($masterItem))->response()->setStatusCode(201);
    }
    public function get($id): MasterItemResource
    {
        $user = Auth::user();
        $masterItem = $this->getItem($id);

        return new MasterItemResource($masterItem);
    }

    public function getAll(): MasterItemCollection
    {
        $user = Auth::user();

        $masterItem = MasterItem::query()->orderByDesc('active_flag')
                                         ->orderBy('item_name')
                                         ->get();

        return new MasterItemCollection($masterItem);
    }

    public function getItemByItemType($itemType): MasterItemCollection
    {
        $user = Auth::user();

        $masterItem = MasterItem::query()->where('item_type', $itemType)
                                         ->orderByDesc('active_flag')
                                         ->orderBy('item_name')
                                         ->get();

        return new MasterItemCollection($masterItem);
    }

    public function update($id, MasterItemUpdateRequest $request): MasterItemResource
    {
        $user = Auth::user();
        $masterItem = $this->getItem($id);
        $data = $request->validated();
        
        if($data['item_name'] != $masterItem->item_name) {
            $this->checkItemExists($data['item_name']);
        }

        if($data['item_code'] != $masterItem->item_code) {
            $this->checkItemCodeExists($data['item_code']);
        }

        $masterItem->fill($data);

        $masterItem->updated_by = $user->id;
        $masterItem->save();

        return new MasterItemResource($masterItem);
    }

    public function delete($id): JsonResponse
    {
        $user = Auth::user();
        $masterItem = $this->getItem($id);
        $masterItem->delete();

        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }

    public function search(Request $request):MasterItemCollection
    {
        $user = Auth::user();
        $page = $request->get('page',1);
        $size = $request->get('size',10);

        $masterItem = MasterItem::query();

        $masterItem = $masterItem->where(function (Builder $builder) use ($request)
        {
            $itemName = $request->input('item_name');
            if($itemName){
                $builder->where('item_name','like','%'.$itemName.'%');
            }

            $itemCode = $request->input('item_code');
            if($itemCode){
                $builder->where('item_code','like','%'.$itemCode.'%');
            }

            // $category = $request->input('category');
            // if($category){
            //     $builder->where('category','like','%'.$category.'%');
            // }
        });

        $masterItem = $masterItem->paginate(perPage: $size, page: $page);

        return new MasterItemCollection($masterItem);
    }

    public function inactiveItem($id): MasterItemResource {
        $user = Auth::user();
        $masterItem = $this->getItem($id);

        if($masterItem->active_flag == 'Y') {
            $masterItem->active_flag = 'N';
        } else {
            $masterItem->active_flag = 'Y';
        }

        $masterItem->inactive_date = Carbon::now();
        $masterItem->updated_by = $user->id;
        $masterItem->save();

        return new MasterItemResource($masterItem);
    }
}
