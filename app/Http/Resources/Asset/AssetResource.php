<?php

namespace App\Http\Resources\Asset;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetResource extends JsonResource
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
            'owner_id' => $this->owner_id,
            'owner_name' => $this->name,
            'item_id' => $this->item_id,
            'item_name' => $this->item_name,
            'quantity' => $this->quantity,
            'cogs' => $this->cogs,
            'selling_price' => $this->selling_price,
            'description' => $this->description,
            'created_at' => $this->created_at->format('d M Y H:i'),
        ];
    }
}
