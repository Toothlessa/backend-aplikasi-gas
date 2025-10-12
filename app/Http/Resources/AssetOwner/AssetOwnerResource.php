<?php

namespace App\Http\Resources\AssetOwner;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetOwnerResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'active_flag' => $this->active_flag,
            'inactive_date' => $this->inactive_date,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at?->format('d M Y H:i'),
            'updated_at' => $this->updated_at?->format('d M Y H:i'),

        ];
    }
}
