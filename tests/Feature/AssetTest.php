<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetOwner;
use Database\Seeders\AssetOwnerSeeder;
use Database\Seeders\AssetSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class AssetTest extends TestCase
{

    public function testCreateSuccess() {

        $this->seed( [UserSeeder::class, AssetOwnerSeeder::class]);

        $assetOwner = AssetOwner::query()->limit(1)->first();

        $this->post('/api/assets', [
            'owner_id' => $assetOwner->id,
            'asset_name' => 'Gas 3 Kg',
            'quantity' => 100,
            'cogs' => 140000,
            'selling_price' => 160000,
            'description' => 'Beli di Mas Supar',
        ], [
            'Authorization' => 'test'
        ])->assertStatus(201)
        ->assertJson([
            'data' => [
                'asset_name' => 'Gas 3 Kg',
                'quantity' => 100,
                'cogs' => 140000,
                'selling_price' => 160000,
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

        $assets = Asset::query()->first();
        $response = $this->get('api/assets/details/'.$assets->owner_id.'/assets/'.$assets->asset_name,
        [
            'Authorization' => 'test'
        ])->assertStatus(status:200)
        ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testUpdateSuccess() {

        $this->testCreateSuccess();

        $assets = Asset::query()->first();
        $assetOwner = AssetOwner::query()->latest()->first();

        $this->patch('/api/assets/'.$assets->id, [
            'owner_id' => $assetOwner->id,
            'asset_name' => 'Gas 4 Kg',
            'quantity' => 10,
            'cogs' => 140000,
            'selling_price' => 160000,
            'description' => 'Beli di Pak Kandar',
        ], [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->assertJson([
            'data' => [
                'asset_name' => 'Gas 4 Kg',
                'quantity' => 10,
                'cogs' => 140000,
                'selling_price' => 160000,
                'description' => 'Beli di Pak Kandar',
            ]
            ]);
    }
}
