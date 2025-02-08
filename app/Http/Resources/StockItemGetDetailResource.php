<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockItemGetDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' =>$this->id,
            'item_id' =>$this->item_id,
            'item_name' =>$this->item_name,
            'item_code' =>$this->item_code,
            'category' =>$this->category,
            'stock' =>$this->stock,
            'created_at' => date("d F Y h:i:s", strtotime($this->created_at)),
        ];
    }
}
