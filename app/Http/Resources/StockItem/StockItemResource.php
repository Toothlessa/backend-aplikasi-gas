<?php

namespace App\Http\Resources\StockItem;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        
        return [
            'item_id' => $this->item_id,
            'stock' => $this->stock,
        ];
    }
}
