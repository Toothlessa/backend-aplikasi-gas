<?php

namespace App\Http\Resources\Debt;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DebtSummaryResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'customer_name' => $this->customer->customer_name,
            'total_pay' => $this->total_pay,
            'total_debt' => $this->total_debt,
            'debt_left' => $this->debt_left,
        ];
    }
}
