<?php

namespace App\Http\Resources;

use App\Models\AssetOwner;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetCreateResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        $assetowner = AssetOwner::where('owner_id', $this->owner_id)->first();
        return [
            'id' => $this->id,
            'owner_name' => $assetowner->name,
            'asset_name' => $this->asset_name,
            'quantity' => $this->quantity,
            'cogs' => $this->cogs,
            'selling_price' => $this->selling_price,
            'description' => $this->description,
            'created_at' => $this->created_at,

        ];
    }
}
