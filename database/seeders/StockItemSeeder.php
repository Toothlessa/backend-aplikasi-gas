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
        $user = User::query()->first();
        $masterItem = MasterItem::query()->first();
        
        StockItem::create([
            'item_id' => $masterItem->id,
            'stock' => 400,
            'cogs' => 5000,
            'selling_price' => 10000,
            'created_by' => $user->id,
        ]);
    }
}
