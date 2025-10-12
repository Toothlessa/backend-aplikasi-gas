<?php

namespace App\Http\Resources\MasterItem;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MasterItemResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'item_name' => $this->item_name,
            'item_code' => $this->item_code,
            'item_type' => $this->item_type,
            'category_id' =>$this->category_id,
            'cost_of_goods_sold' => $this->cost_of_goods_sold,
            'selling_price' => $this->selling_price,
            'active_flag' => $this->active_flag,
            'inactive_date' => $this->inactive_date,
            'created_by' => $this->created_by,
            'updated_by' => $this->update_by,
        ];
    }
}
