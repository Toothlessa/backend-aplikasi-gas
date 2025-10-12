<?php

namespace App\Http\Resources\Transaction;

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
            'customer_name' => $customer->customer_name,
            'trx_number' => $this->trx_number,
            'stock_id' => $this->stock_id,
            'quantity' => $this->quantity,
            'amount' => $this->amount,
            'total' => $this->total,
            'description' => $this->description,
            'nik' => $customer->nik,
            'created_by' => $this->created_by,
            'created_at' => date("h:i:s", strtotime($this->created_at)),
        ];
    }
}
