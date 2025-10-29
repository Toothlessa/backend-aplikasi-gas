<?php

namespace App\Services;

use App\Repositories\CategoryItemRepository;
use App\Repositories\StockItemRepository;
use App\Repositories\MasterItemRepository;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class MasterItemService
{
    protected $repository;
    protected $stockItemRepository;
    protected $categoryItemRepository;

     public function __construct( MasterItemRepository $repository,
                                  StockItemRepository $stockItemRepository,
                                  CategoryItemRepository $categoryItemRepository) 
    {
        $this->repository = $repository;
        $this->stockItemRepository = $stockItemRepository;
        $this->categoryItemRepository = $categoryItemRepository;
    }

    public function create($data, $user)
    {
        return DB::transaction(function() use($data, $user)
        {
           $item_code = $this->generateItemCode($data['category_id']);

           $masterItem = array_merge($data, [
            'item_code' => $item_code,
            'created_by' => $user->id,
           ]);

           $this->validateMasterItemExists($masterItem['item_name']);
           $this->validateItemCodeExists($masterItem['item_code']);

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

            $newData = array_merge($data, [
                'updated_by' => $user->id,
            ]);

            return $this->repository->update($masterItem, $newData);
        });
    }

    public function generateItemCode($categoryId)
    {
        $categoryItem = $this->categoryItemRepository->findById($categoryId);
        
        if(!$categoryItem){
            throw new HttpResponseException(response()->json([
                'errors' => 'CATEGORY_NOT_FOUND',
            ])->setStatusCode(404));
        }
        
        $lastSeq = $this->repository->getLastSequenceByCategoryId($categoryId);

        $item_code = sprintf('%s%03d', $categoryItem->prefix, $lastSeq + 1 );

        return $item_code;
    }

    public function findById(int $id)
    {
        $masterItem = $this->repository->findById($id);

        if (!$masterItem) {
            throw new HttpResponseException(response()->json([
                "errors" => "MASTER_ITEM_NOT_FOUND"
            ], 404));
        }

        return $masterItem;
    }

    public function getAll()
    {
        $masterItem = $this->repository->getAll();

         if (!$masterItem ) {
            throw new HttpResponseException(response()->json([
                'NO_DATA_FOUND_IN_MASTER_ITEM'
            ])->setStatusCode(404));
        }

        return $masterItem;
    }

    public function getItemByItemType($itemType)
    {
        $masterItem = $this->repository->getItemByItemType($itemType);

        if(!$masterItem) {
            throw new HttpResponseException(response()->json([
                'errors' => 'NO_DATA_FOUND_ITEM_WITH_MASTER_TYPE'
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
                'errors' => 'ITEM_NAME_EXISTS',
            ])->setStatusCode(400));
        }

        return $masterItem;
    }

    public function validateItemCodeExists($itemCode)
    {
        $masterItem = $this->repository->validateItemCodeExists($itemCode);

        if($masterItem){
            throw new HttpResponseException(response()->json([
                'errors' => 'ITEM_CODE_EXISTS',
            ])->setStatusCode(400));
        }

        return $masterItem;
    }

    public function inactiveItem($id, $user)
    {
        $masterItem = $this->repository->findById($id);

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