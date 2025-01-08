<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MasterItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item_name' => $this->item_name,
            'item_code' => $this->item_code,
            'category' => $this->category,
            'cost_of_goods_sold' => $this->cost_of_goods_sold,
            'selling_price' => $this->selling_price,
        ];
    }
}
