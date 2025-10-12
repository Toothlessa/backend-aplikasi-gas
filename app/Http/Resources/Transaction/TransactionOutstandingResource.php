<?php

namespace App\Http\Resources\Transaction;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionOutstandingResource extends JsonResource
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
            'customer_id' => $this->customer_id,
            'stock_id' => $this->stock_id,
            'customer_name' => $this->customer->customer_name,
            'item_name' => $this->masterItem->item_name,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'amount' => $this->amount,
            'total' => $this->total,
            'created_at' => date("d-m-y", strtotime($this->created_at)),
        ];
    }
}
