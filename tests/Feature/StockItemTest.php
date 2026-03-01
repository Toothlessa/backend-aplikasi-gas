<?php

namespace Tests\Feature;

use App\Models\MasterItem;
use App\Models\StockItem;
use Database\Seeders\AssetOwnerSeeder;
use Database\Seeders\AssetSeeder;
use Database\Seeders\CategoryItemSeeder;
use Database\Seeders\MasterItemSeeder;
use Database\Seeders\StockItemSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class StockItemTest extends TestCase
{
    
    public function testCreateNewStockSuccess()
    {
        $this->seed([
            UserSeeder::class, 
            CategoryItemSeeder::class, 
            MasterItemSeeder::class]);
        
        $masterItem = MasterItem::query()->first();

        $this->post('/api/stockitems/' .($masterItem->id), [
            'stock' => 300,
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 201)
        ->assertJson([
            'data' => [
                'stock' => 300,
            ]
        ]);
    }

    public function testInputStockMinus(){
        $this->testCreateNewStockSuccess();
        $masterItem = MasterItem::query()->first();

        $payload = [
            'stock' => -1
        ];

        $this->postJson(
            '/api/stockitems/' .($masterItem->id),
            $payload,
            ['Authorization' => 'test']
        )
        ->assertStatus(400)
        ->assertJson([
            'errors' => [
                'stock' => [
                    'The stock field must be at least 1.'
                ]
            ]
        ]);
    }

    public function testUpdateStockSuccess()
    {
        $this->testCreateNewStockSuccess();
        $masterItem = MasterItem::query()->first();
        $stockInput = StockItem::where('item_id', $masterItem->id)->first();

        $this->put('/api/stockitems/' .$stockInput->id, 
        [ 
            'stock' => '560'
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 200)
        ->assertJson([
           'data' => [
            'item_id' => $masterItem->id,
            'stock' => '560'
            ]
        ]);
    }

    public function testGetCurrentStock()
    {
        $this->testCreateNewStockSuccess();

        $masterItem = MasterItem::query()->first();
        $response = $this->get('api/stockitems/current/'.$masterItem->id, 
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testGetCurrentNoParameterStock()
    {
        $this->testCreateNewStockSuccess();

        // $masterItem = MasterItem::query()->first();
        $response = $this->get('api/stockitems/current/',
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testGetDetailStock()
    {
        $this->testCreateNewStockSuccess();
        $masterItem = MasterItem::query()->first();

        $response = $this->get('api/stockitems/detail/'.$masterItem->id, 
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testGetDetailStockNotFound() {
        $this->testCreateNewStockSuccess();
        $stock = StockItem::query()->orderByDesc('id')->first();

        $this->get('api/stockitems/detail/'.$stock->id+100, 
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 404)
        ->assertJson([
            'error' => 'DETAIL_STOCK_NOT_FOUND',
        ]);
    }

    public function testGetDisplayStock() {

        $this->seed([UserSeeder::class, CategoryItemSeeder::class,  AssetOwnerSeeder::class, 
                            MasterItemSeeder::class, StockItemSeeder::class, AssetSeeder::class]);
        
        $filledGas = MasterItem::where('item_name', 'GAS LPG 3KG')->first();

        $emptyGas  = MasterItem::where('item_name', 'GAS LPG 3KG KOSONG')->first();

        $response = $this->get('api/stockitems/display/'.$filledGas->id.'/'.$emptyGas->id, 
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

}
