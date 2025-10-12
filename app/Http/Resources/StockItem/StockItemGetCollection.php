<?php

namespace App\Http\Resources\StockItem;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class StockItemGetCollection extends ResourceCollection
{

    public function toArray(Request $request): array
    {
        return [
            "data"=> StockItemGetResource::collection($this->collection)
        ];
    }
}
