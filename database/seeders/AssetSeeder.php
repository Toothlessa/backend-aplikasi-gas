<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetOwner;
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
        Asset::create([
            'owner_id' => $assetOwner->id,
            'asset_name' => 'Gas 3 Kg',
            'quantity' => 100,
            'cogs' => 14000000,
            'selling_price' => 16000000,
            'description' => 'Beli di Mas Supar',
        ]);

        $assetOwner = AssetOwner::query()->first();
        Asset::create([
            'owner_id' => $assetOwner->id,
            'asset_name' => 'Gas 3 Kg',
            'quantity' => 100,
            'cogs' => 14000000,
            'selling_price' => 16000000,
            'description' => 'Beli di A Andri',
        ]);
    }
}
