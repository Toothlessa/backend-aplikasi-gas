<?php

namespace App\Http\Resources\StockItem;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockItemGetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        
        return [
            'item_id' =>$this->item_id,
            'item_name' => $this->item_name,
            'item_code' => $this->item_code,
            'category' => $this->category,
            'cogs' => $this->cost_of_goods_sold,
            'selling_price' => $this->selling_price,
            'total_stock' => $this->total_stock,
        ];
    }
}
