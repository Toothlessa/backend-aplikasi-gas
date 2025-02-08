<?php

namespace App\Http\Resources;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $customer = Customer::where("id", $this->customer_id)->first();
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'trx_number' => $this->trx_number,
            'quantity' => $this->quantity,
            'amount' => $this->amount,
            'total' => $this->total,
            'description' => $this->description,
            'customer_name' => $customer->customer_name,
            'nik' => $customer->nik,
            'created_by' => $this->created_by,
            // 'created_at' => $this->created_at,
            'created_at' => date("h:i:s", strtotime($this->created_at)),
        ];
    }
}
