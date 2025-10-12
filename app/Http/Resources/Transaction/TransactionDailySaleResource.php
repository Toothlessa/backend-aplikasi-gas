<?php

namespace App\Http\Resources\Transaction;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionDailySaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'month'     => $this['month'],
            'day'       => $this['day'], // sesuai nama di function kamu
            'total'     => $this['total'],
        ];
    }
}