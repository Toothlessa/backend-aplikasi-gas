<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetGetSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'owner_id' => $this->owner_id,
            'owner_name' => $this->name,
            'asset_name' => $this->asset_name,
            'quantity' => $this->quantity,
            'cogs' => $this->cogs,
            'selling_price' => $this->selling_price,
        ];
    }
}
