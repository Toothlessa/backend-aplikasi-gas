<?php

namespace App\Http\Resources;

use App\Models\CategoryItem;
use App\Models\StockItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MasterItemResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        $categoryItem = CategoryItem::find($this->category_id);
        $stock = StockItem::query()->where('item_id', $this->id)->sum('stock');

        if($stock < 3) {
            $inStock = 'N';
        } else {
            $inStock = 'Y';
        }

        return [
            'id' => $this->id,
            'item_name' => $this->item_name,
            'item_code' => $this->item_code,
            'item_type' => $this->item_type,
            'category_id' => $categoryItem->id,
            'category' => $categoryItem->name,
            'cost_of_goods_sold' => $this->cost_of_goods_sold,
            'selling_price' => $this->selling_price,
            'in_stock' => $inStock,
            'active_flag' => $this->active_flag,
        ];
    }
}
