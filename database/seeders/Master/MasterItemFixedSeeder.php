<?php

namespace Database\Seeders\Master;

use App\Models\CategoryItem;
use App\Models\MasterItem;
use Illuminate\Database\Seeder;

class MasterItemFixedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryItem = CategoryItem::query()->where('name', 'Bahan Pokok')->first();

        MasterItem::create(
            [
                'item_name' => 'GAS LPG 3KG ISI',
                'item_code' => 'BP01',
                'item_type' => 'ITEM',
                'category_id' => $categoryItem->id,
                'cost_of_goods_sold' => 16000,
                'selling_price' => 19000,
            ]
        );

        MasterItem::create(
            [
                'item_name' => 'GAS LPG 3KG KOSONG',
                'item_code' => 'BP02',
                'item_type' => 'ASSET',
                'category_id' => $categoryItem->id,
                'cost_of_goods_sold' => 160000,
                'selling_price' => 181000,
            ]
        );
    }
}
