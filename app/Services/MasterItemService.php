<?php

namespace App\Services;

use App\Repositories\MasterItemRepository;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class MasterItemService
{
    protected $repository;
    protected $categoryItemService;

     public function __construct( MasterItemRepository $repository,
                                  CategoryItemService $categoryItemService) 
    {
        $this->repository = $repository;
        $this->categoryItemService = $categoryItemService;
    }

    public function create($data, $user)
    {
        return DB::transaction(function() use($data, $user)
        {
            $this->validateMasterItemExists($data['item_name']);
            $item_code = $this->generateItemCode($data['category_id']);

            $this->validateItemCodeExists($item_code);

            $masterItem = [
                'item_name'          => $data['item_name'],
                'item_code'          => $item_code,
                'item_type'          => $data['item_type'],
                'category_id'        => $data['category_id'],
                'cost_of_goods_sold' => $data['cost_of_goods_sold'],
                'selling_price'      => $data['selling_price'],
                'created_by'         => $user->id,
            ];

            return $this->repository->create($masterItem);
        });
    }

    public function update($id, $data, $user)
    {
        return DB::transaction(function() use($id, $data, $user)
        {
            $masterItem = $this->findById($id);

            if($data['item_name'] != $masterItem->item_name) {
                $this->validateMasterItemExists($data['item_name']);
            }

            if($data['category_id'] != $masterItem->category_id) {
                $data['item_code'] = $this->generateItemCode($data['category_id']);
            }

            $newMasterItem = [
                'item_name'             => $data['item_name'],
                'item_code'             => $masterItem->item_code,
                'item_type'             => $data['item_type'],
                'category_id'           => $data['category_id'],
                'cost_of_goods_sold'    => $data['cost_of_goods_sold'],
                'selling_price'         => $data['selling_price'],
                'updated_by'            => $user->id,
            ];

            return $this->repository->update($masterItem, $newMasterItem);
        });
    }

    public function generateItemCode($categoryId)
    {
        $categoryItem = $this->categoryItemService->findById($categoryId);
        
        if(!$categoryItem){
            throw new HttpResponseException(response()->json([
                'errors' => 'CATEGORY_NOT_FOUND',
            ])->setStatusCode(404));
        }
    
        $lastid = $this->getLastSequenceByCategoryId($categoryId);

        $item_code = sprintf('%s%03d', $categoryItem->prefix, $lastid + 1 );

        return $item_code;
    }

    public function findById(int $id)
    {
        $masterItem = $this->repository->findById($id);

        if (!$masterItem) {
            throw new HttpResponseException(response()->json([
                "error" => "MASTER_ITEM_NOT_FOUND"
            ], 404));
        }

        return $masterItem;
    }

    public function getLastSequenceByCategoryId(int $categoryId)
    {
        $lastSeq = $this->repository->getLastSequenceByCategoryId($categoryId);

        if(!$lastSeq) {
            return 0;
        }

        return $lastSeq;
    }

    public function getAll()
    {
        $masterItem = $this->repository->getAll();

         if (!$masterItem ) {
            throw new HttpResponseException(response()->json([
                'error' => 'NO_DATA_FOUND_IN_MASTER_ITEM'
            ])->setStatusCode(404));
        }

        return $masterItem;
    }

    public function getItemByItemType($itemType)
    {
        $masterItem = $this->repository->getItemByItemType($itemType);

        if(!$masterItem) {
            throw new HttpResponseException(response()->json([
                'error' => 'NO_DATA_FOUND_ITEM_WITH_MASTER_TYPE'
            ])->setStatusCode(404));
        }

        return $masterItem;
    }

    public function getItemByFlagStatus($flagStatus)
    {
        $masterItem = $this->repository->getItemByFlagStatus($flagStatus);

        if(!$masterItem) {
            throw new HttpResponseException(response()->json([
                'error' => 'NO_ITEM_FOUND_FOR_THAT_STATUS',
            ])->setStatusCode(404));
        }

        return $masterItem;
    }

    public function validateMasterItemExists($itemName)
    {
        $masterItem = $this->repository->validateMasterItemExists($itemName);

        if($masterItem) {
            throw new HttpResponseException(response()->json([
                'error' => 'ITEM_NAME_EXISTS',
            ])->setStatusCode(400));
        }

        return $masterItem;
    }

    public function validateItemCodeExists($itemCode)
    {
        $masterItem = $this->repository->validateItemCodeExists($itemCode);

        if($masterItem){
            throw new HttpResponseException(response()->json([
                'error' => 'ITEM_CODE_EXISTS',
            ])->setStatusCode(400));
        }

        return $masterItem;
    }

    public function inactiveItem($id, $user)
    {
        $masterItem = $this->findById($id);

        if($masterItem->active_flag == 'Y') {
            $masterItem->active_flag = 'N';
            $masterItem->inactive_date = Carbon::now(); 
        } else {
            $masterItem->active_flag = 'Y';
            $masterItem->inactive_date = NULL;
        }

        $masterItem->updated_by = $user->id;
        $masterItem->save();
        return $masterItem;
    }

}