<?php

namespace App\Http\Controllers;

use App\Http\Requests\MasterItemCreateRequest;
use App\Http\Requests\MasterItemUpdateRequest;
use App\Http\Resources\MasterItemCollection;
use App\Http\Resources\MasterItemResource;
use App\Models\MasterItem;
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
                "errors" => [
                    'message' => [
                        'not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        return $masterItem;
    }

    public function checkItemExists(string $itemName)
    {
        if(MasterItem::where("item_name", $itemName)->count()==1){
            throw new HttpResponseException(response([
                "errors"=> [
                    "item_name" => [
                        "nama item sudah terdaftar"
                    ]
                ]
            ], 400));
        }
    } 

    public function checkItemCodeExists(string $itemCode)
    {
        if(MasterItem::where("item_code", $itemCode)->count()==1){
            throw new HttpResponseException(response([
                "errors"=> [
                    "item_code" => [
                        "kode item sudah terdaftar"
                    ]
                ]
            ], 400));
        }
    } 

    public function create(MasterItemCreateRequest $request): JsonResponse
    {
        $user = Auth::user();
        $data = $request->validated();

        $this->checkItemExists($data['item_name']);
        $this->checkItemCodeExists($data['item_code']);

        $masterItem = new MasterItem($data);
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

    public function update($id, MasterItemUpdateRequest $request): MasterItemResource
    {
        $user = Auth::user();
        $masterItem = $this->getItem($id);
        $data = $request->validated();

        $masterItem->fill($data);

        $this->checkItemExists($data['item_name']);
        $this->checkItemCodeExists($data['item_code']);

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

            $category = $request->input('category');
            if($category){
                $builder->where('category','like','%'.$category.'%');
            }
        });

        $masterItem = $masterItem->paginate(perPage: $size, page: $page);

        return new MasterItemCollection($masterItem);
    }

}
