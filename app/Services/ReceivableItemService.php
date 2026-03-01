<?php

namespace App\Services;

use App\Repositories\ReceivableItemRespository;
use DB;
use Illuminate\Http\Exceptions\HttpResponseException;

class ReceivableItemService
{
    protected $receivableItemRespository;

    public function __construct(ReceivableItemRespository $receivableItemRespository)
    {
        $this->receivableItemRespository = $receivableItemRespository;
    }

    public function createReceivableItem($data)
    {
        return $this->receivableItemRespository->createReceivableItem($data);
    }

    public function autoCreateReceivableItemFromTransaction($data) {
            # prepare data receivable item
            $receivableItemData = [
                "receivable_id"     => $data["receivable_id"],
                "item_id"           => $data["item_id"],
                "qty"               => $data["quantity"],
                "price"             => $data["price"],
                #subtotal was calculated in data model
            ];

            return $this->receivableItemRespository->createReceivableItem($receivableItemData);
    }

    public function autoUpdateReceivableItemFromTransaction($receivableId, $data)
    {
        # validate existence data
        $receivableItem = $this->getReceivableItemByReceivableId($receivableId);
        
        # prepare receivable item data
        $receivableItemData = [
            "item_id"       => $data["item_id"],
            "quantity"      => $data["quantity"],
            "price"         => $data["price"],
            #subtotal was calculated in data model
        ];

        return $this->receivableItemRespository->updateReceivableItem($receivableItem, $receivableItemData);
    }

    public function getReceivableItemByReceivableId($receivableId)
    {
        $receivableItem = $this->receivableItemRespository->getReceivableItemByReceivableId($receivableId);

        if(! $receivableItem) {
            throw new HttpResponseException(response()->json([
                'error' => 'RECEIVABLE_ITEM_NOT_FOUND',
            ], 404));
        }

        return $receivableItem;
    }
}