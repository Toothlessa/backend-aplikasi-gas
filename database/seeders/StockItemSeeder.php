<?php

namespace Database\Seeders;

use App\Models\MasterItem;
use App\Models\StockItem;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StockItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where("username", 'test0')->first();
        $masterItem = MasterItem::query()->limit(1)->first();
        
        StockItem::create([
            'item_id' => $masterItem->id,
            'stock' => 400,
            'cogs' => 5000,
            'selling_price' => 10000,
            'created_by' => $user->id,
        ]);

        // $masterItem = MasterItem::where('item_name', 'GAS LPG 3KG')->first();
        // StockItem::create([
        //     'item_id' => $masterItem->id,
        //     'stock' => 400,
        //     'cogs' => 5000,
        //     'selling_price' => 10000,
        //     'created_by' => $user->id,
        // ]);
    }
}
