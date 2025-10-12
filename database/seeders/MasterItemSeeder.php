<?php

namespace Database\Seeders;

use App\Models\CategoryItem;
use App\Models\MasterItem;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->first();
        $category = CategoryItem::query()->first();

        MasterItem::create([
            'item_name' => 'GAS LPG 3KG',
            'item_type' => 'ITEM',
            'item_code' => 'BP001',
            'category_id' => $category->id,
            'cost_of_goods_sold' => 5000,
            'selling_price' => 10000,
            'created_by' => $user->id,
        ]);

        MasterItem::create([
            'item_name' => 'GAS LPG 3KG KOSONG',
            'item_type' => 'ASSET',
            'item_code' => 'BP002',
            'category_id' => $category->id,
            'cost_of_goods_sold' => 5000,
            'selling_price' => 10000,
            'created_by' => $user->id,
        ]);
    }
}
