<?php

namespace App\Http\Resources\Transaction;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TransactionDailyCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            "data"=> TransactionDailySaleResource::collection($this->collection)
        ];
    }
}
