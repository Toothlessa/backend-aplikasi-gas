<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetOwner;
use App\Models\MasterItem;
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

        $this->seed( [
                    UserSeeder::class, 
                    CategoryItemSeeder::class, 
                    AssetOwnerSeeder::class, 
                    MasterItemSeeder::class, 
                ]);
            
        $assetOwner = AssetOwner::query()->first();
        $masterItem = MasterItem::query()->first();

        $this->post('/api/assets', [
            'owner_id'              => $assetOwner->id,
            'item_id'               => $masterItem->id,
            'quantity'              => 100,
            'cogs'    => 10000,
            'selling_price'         => 20000,
            'description'           => 'Beli di Mas Supar',
        ], [
            'Authorization' => 'test'
        ])->assertStatus(201)
        ->assertJson([
            'data' => [
                'owner_id'              => $assetOwner->id,
                'item_id'               => $masterItem->id,
                'quantity'              => 100,
                'cogs'                  => 10000,
                'selling_price'         => 20000,
                'description'           => 'Beli di Mas Supar',
            ]
            ]);
    }

    public function testUpdateSuccess () {

        $this->testCreateSuccess();
        $asset = Asset::query()->first();

        $payload = [
            'owner_id'              => $asset->owner_id,
            'item_id'               => $asset->item_id,
            'quantity'              => 8,
            'cogs'                  => 10000,
            'selling_price'         => 20000,
            'description'           => 'Beli di Pak Kandar',
        ];

        $this->patchJson(
            "/api/assets/{$asset->id}", 
            $payload, 
            ['Authorization' => 'test']
            )
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'owner_id'              => $asset->owner_id,
                'item_id'               => $asset->item_id,
                'quantity'              => 8,
                'cogs'                  => 10000,
                'selling_price'         => 20000,
                'description'           => 'Beli di Pak Kandar',
            ]
            ]);
    }

     public function testUpdateNotFound () {

        $this->testCreateSuccess();
        $asset = Asset::query()->orderByDesc('id')->first();

        $payload = [
            'owner_id'              => $asset->owner_id,
            'item_id'               => $asset->item_id,
            'quantity'              => 8,
            'cogs'                  => 10000,
            'selling_price'         => 20000,
            'description'           => 'Beli di Pak Kandar',
        ];

        $this->patchJson(
            "/api/assets/" .$asset->id + 100, 
            $payload, 
            ['Authorization' => 'test']
            )
        ->assertStatus(404)
        ->assertJson([
            "error" => "ASSET_NOT_FOUND"
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
        $response = $this->get('api/assets/details/'.$asset->owner_id.'/'.$asset->item_id,
        [
            'Authorization' => 'test'
        ])->assertStatus(status:200)
        ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }
}
