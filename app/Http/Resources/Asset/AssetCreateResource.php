<?php

namespace App\Http\Resources\Asset;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetCreateResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'owner_id' => $this->owner_id,
            'item_id' => $this->item_id,
            'quantity' => $this->quantity,
            'cogs' => $this->cogs,
            'selling_price' => $this->selling_price,
            'description' => $this->description,
            'created_at' => $this->created_at,
        ];
    }
}
