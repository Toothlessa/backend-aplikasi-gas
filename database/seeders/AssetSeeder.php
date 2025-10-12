<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetOwner;
use App\Models\MasterItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assetOwner = AssetOwner::query()->latest('id')->first();
        $masteritem = MasterItem::query()->latest('id')->first();

        Asset::create([
            'owner_id' => $assetOwner->id,
            'item_id' => $masteritem->id,
            'quantity' => 1000,
            'cogs' => 14000000,
            'selling_price' => 16000000,
            'description' => 'Asset Modal',
        ]);

        Asset::create([
            'owner_id' => $assetOwner->id,
            'item_id' => $masteritem->id,
            'quantity' => 100,
            'cogs' => 14000000,
            'selling_price' => 16000000,
            'description' => 'Beli di Mas Supar',
        ]);

        $assetOwner = AssetOwner::query()->first();
        Asset::create([
            'owner_id' => $assetOwner->id,
            'item_id' => $masteritem->id,
            'quantity' => 100,
            'cogs' => 14000000,
            'selling_price' => 16000000,
            'description' => 'Beli di A Andri',
        ]);
    }
}
