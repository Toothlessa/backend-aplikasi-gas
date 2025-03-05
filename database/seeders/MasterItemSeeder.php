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
        $user = User::where("username", 'test0')->first();
        $category = CategoryItem::query()->first();

        MasterItem::create([
            'item_name' => 'GAS LPG 3KG',
            'item_code' => 'test00',
            'category_id' => $category->id,
            'cost_of_goods_sold' => 5000,
            'selling_price' => 10000,
            'created_by' => $user->id,
        ]);

        MasterItem::create([
            'item_name' => 'GAS LPG 3KG KOSONG',
            'item_code' => 'test000',
            'category_id' => $category->id,
            'cost_of_goods_sold' => 5000,
            'selling_price' => 10000,
            'created_by' => $user->id,
        ]);

        MasterItem::create([
            'item_name' => 'test',
            'item_code' => 'test001',
            'category_id' => $category->id,
            'cost_of_goods_sold' => 5000,
            'selling_price' => 10000,
            'created_by' => $user->id,
        ]);

        MasterItem::create([
            'item_name' => 'test1',
            'item_code' => 'test002',
            'category_id' => $category->id,
            'cost_of_goods_sold' => 5000,
            'selling_price' => 10000,
            'created_by' => $user->id,
        ]);
    }
}
