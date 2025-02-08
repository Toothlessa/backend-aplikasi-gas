<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DebtGetSummaryResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'customer_id' => $this->customer_id,
            'customer_name' => $this->customer_name,
            'total_pay' => $this->total_pay,
            'total_debt' => $this->total_debt,
            'debt_left' => $this->debt_left,
        ];
    }
}
