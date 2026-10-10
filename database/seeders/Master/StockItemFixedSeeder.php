<?php

namespace Database\Seeders\Master;

use Illuminate\Database\Seeder;
use App\Models\StockItem;
use App\Models\MasterItem;
use App\Models\User;


class StockItemFixedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->first();
        $item = MasterItem::query()->first();
        StockItem::create([
            'item_id' => $item->id,
            'stock' => 10000,
            'cogs' => 16000,
            'selling_price' => 19000,
            'created_by' => $user->id,
        ]);
    }
}
