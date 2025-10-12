<?php

namespace App\Repositories;

use App\Models\MasterItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MasterItemRepository
{
    public function create($data)
    {
        return MasterItem::create($data);
    }

    public function update(MasterItem $masterItem, $data)
    {
        $masterItem->fill($data);
        $masterItem->save();

        return $masterItem;
    }

    public function findById(int $itemId)
    {
        return MasterItem::find($itemId); 
    }

    public function findAll()
    {
        return MasterItem::all();
    }

    public function getItemByItemType($itemType):Collection
    {
        return MasterItem::where('item_type', $itemType)->get();
    }

    public function getItemByFlagStatus($flagStatus)
    {
        return MasterItem::where('active_flag', $flagStatus)->get();
    }

    public function getMItemByItemName($itemName)
    {
        return MasterItem::whereRaw("upper(item_name) = 'GAS LPG 3KG ISI'")->first();
    }

    public function getLastSequenceByCategoryId($categoryId)
    {
        return MasterItem::where('category_id', $categoryId)
                          ->count();
    }

    public function validateMasterItemExists(string $itemName)
    {
        return MasterItem::where('item_name', $itemName)->exists();
    } 

    public function validateItemCodeExists($itemCode)
    {
        return MasterItem::where('item_code', $itemCode)->exists();
    }

}