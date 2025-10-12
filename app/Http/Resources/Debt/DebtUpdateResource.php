<?php

namespace App\Http\Resources\Debt;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DebtUpdateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'description' => $this->description,
            'amount_pay' => $this->amount_pay,
            'total' => $this->total,
            'created_by' => $this->created_by,
            'created_at' => date("d F Y h:i:s", strtotime($this->created_at)),
        ];
    }
}
