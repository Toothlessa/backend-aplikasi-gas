<?php

namespace App\Http\Resources\Debt;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DebtResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'customer_name' => $this->customer->customer_name,
            'description' => $this->description,
            'amount_pay' => $this->amount_pay,
            'total' => $this->total,
            'created_at' => date("d F Y h:i:s", strtotime($this->created_at)),
        ];
    }
}
