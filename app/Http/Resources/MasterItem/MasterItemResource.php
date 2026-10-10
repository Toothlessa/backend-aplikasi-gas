<?php

namespace App\Http\Resources\MasterItem;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MasterItemResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item_name'         => $this->item_name,
            'item_code'         => $this->item_code,
            'item_type'         => $this->item_type,
            'owner_id'          => $this->owner_id,
            'owner_name'        => $this->assetOwner?->name,
            'category_id'       => $this->category_id,
            'category_name'     => $this->categoryItem->name,
            'in_stock'          => $this->in_stock,
            'cost_of_goods_sold' => $this->cost_of_goods_sold,
            'selling_price'     => $this->selling_price,
            'active_flag'       => $this->active_flag,
            'inactive_date'     => $this->inactive_date,
            'created_by'        => $this->created_by,
            'updated_by'        => $this->updated_by,
        ];
    }
}
