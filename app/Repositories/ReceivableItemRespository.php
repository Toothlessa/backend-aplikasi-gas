<?php

namespace App\Repositories;

use App\Models\ReceivableItem;

class ReceivableItemRespository
{
    public function createReceivableItem($data)
    {
        return ReceivableItem::create($data);
    }

    public function updateReceivableItem(ReceivableItem $receivableItem, $data)
    {
        // return $receivableItem->update($data);
        $receivableItem->fill($data);
        $receivableItem->save();

        return $receivableItem;
    }

    public function getReceivableItemByReceivableId($receivableId)
    {
        return ReceivableItem::where('receivable_id', $receivableId)->first();
    }
}