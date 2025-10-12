<?php

namespace App\Http\Resources\Debt;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DebtCollection extends ResourceCollection
{
   
    public function toArray(Request $request): array
    {
        return [
            "data"=> DebtResource::collection($this->collection)
        ];
    }
}
