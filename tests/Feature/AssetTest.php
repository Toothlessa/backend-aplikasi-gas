<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetOwner;
use App\Models\MasterItem;
use App\Models\User;
use Database\Seeders\AssetOwnerSeeder;
use Database\Seeders\AssetSeeder;
use Database\Seeders\CategoryItemSeeder;
use Database\Seeders\MasterItemSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class AssetTest extends TestCase
{

    public function testCreateSuccess() {

        $this->seed( [UserSeeder::class, CategoryItemSeeder::class, 
                            AssetOwnerSeeder::class, MasterItemSeeder::class, ]);
            
        $assetOwner = AssetOwner::query()->first();
        $masterItem = MasterItem::query()->first();

        $this->post('/api/assets', [
            'owner_id' => $assetOwner->id,
            'item_id' => $masterItem->id,
            'quantity' => 100,
            'description' => 'Beli di Mas Supar',
        ], [
            'Authorization' => 'test'
        ])->assertStatus(201)
        ->assertJson([
            'data' => [
                'owner_id' => $assetOwner->id,
                'item_id' => $masterItem->id,
                'quantity' => 100,
                'cogs' => $masterItem->cost_of_goods_sold,
                'selling_price' => $masterItem->selling_price * 100,
                'description' => 'Beli di Mas Supar',
            ]
            ]);
    }

    public function testGetSummaryOwnerAsset() {
        $this->testCreateSuccess();
        $this->seed( [AssetSeeder::class]);

        $response = $this->get('api/assets/summary',
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 200)
        ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testGetDetailAssets() {
        $this->testGetSummaryOwnerAsset();

        $asset = Asset::query()->first();
        $response = $this->get('api/assets/details/'.$asset->owner_id.'/assets/'.$asset->item_id,
        [
            'Authorization' => 'test'
        ])->assertStatus(status:200)
        ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testUpdateSuccess() {

        $this->testCreateSuccess();

        $assets = Asset::query()->first();
        $owner = AssetOwner::where('name', 'test3')->first();
        $masterItem = MasterItem::where('item_name', 'GAS LPG 3KG KOSONG')->first();
        User::query()->where('token', 'test')->first();

        $this->patch('/api/assets/'.$assets->id, [
            'owner_id' => $owner->id,
            'item_id' => $masterItem->id,
            'quantity' => 8,
            'description' => 'Beli di Pak Kandar',
        ], [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->assertJson([
            'data' => [
                'owner_id' => $owner->id,
                'item_id' => $masterItem->id,
                'quantity' => 8,
                'description' => 'Beli di Pak Kandar',
            ]
            ]);
    }
}
