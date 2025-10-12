<?php

namespace App\Http\Resources\StockItem;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockItemDisplayStockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'running_stock'   => $this['running_stock'],
            'yesterday_stock' => $this['yeterday_stock'], // sesuai nama di function kamu
            'empty_gas'       => $this['empty_gas'],
            'gas_owned'       => $this['gas_owned'],
        ];
    }
}
