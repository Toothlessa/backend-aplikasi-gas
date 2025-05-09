<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionGetResource extends JsonResource
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
            'quantity' => $this->quantity,
            'amount' => $this->amount,
            'total' => $this->total,
            'description' => $this->description,
            'item_id' => $this->item_id,
            'customer_id' => $this->customer_id,
            'customer_name' => $this->customer_name,
            'nik' => $this->nik,
            'created_by' => $this->created_by,
            'created_at' => date("h:i:s", strtotime($this->created_at)),
        ];
    }
}
