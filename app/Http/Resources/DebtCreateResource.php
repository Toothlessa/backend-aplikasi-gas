<?php

namespace App\Http\Resources;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DebtCreateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $customer = Customer::where("id", $this->customer_id)->first();
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'customer_name' => $customer->customer_name,
            'description' => $this->description,
            'amount_pay' => $this->amount_pay,
            'total' => $this->total,
            'created_by' => $this->created_by,
            'created_at' => date("d F Y h:i:s", strtotime($this->created_at)),
            // 'created_at' => $this->created_at,
        ];
    }
}
