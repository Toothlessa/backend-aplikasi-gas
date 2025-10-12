<?php

namespace Tests\Feature;

use App\Models\MasterItem;
use App\Models\StockItem;
use Carbon\Carbon;
use Database\Seeders\AssetOwnerSeeder;
use Database\Seeders\AssetSeeder;
use Database\Seeders\CategoryItemSeeder;
use Database\Seeders\MasterItemSeeder;
use Database\Seeders\StockItemSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertJson;
use function PHPUnit\Framework\assertNotNull;

class StockItemTest extends TestCase
{
    
    public function testInputStockSuccess()
    {
        $this->seed([UserSeeder::class, CategoryItemSeeder::class, MasterItemSeeder::class]);
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

    public function testUpdateStockSuccess()
    {
        $this->testInputStockSuccess();
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

    public function testGetCurrenStock()
    {
        $this->testInputStockSuccess();

        $masterItem = MasterItem::query()->first();
        $response = $this->get('api/stockitems/currentstock/'.$masterItem->id, 
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testGetCurrentNoParameterStock()
    {
        $this->testInputStockSuccess();

        // $masterItem = MasterItem::query()->first();
        $response = $this->get('api/stockitems/currentstock/',
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testGetDetailStock()
    {
        $this->testInputStockSuccess();
        $masterItem = MasterItem::query()->first();

        $response = $this->get('api/stockitems/detailstock/'.$masterItem->id, 
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testGetDisplayStock() {

        $this->seed([UserSeeder::class, CategoryItemSeeder::class,  AssetOwnerSeeder::class, 
                            MasterItemSeeder::class, StockItemSeeder::class, AssetSeeder::class]);
        
        $filledGas = MasterItem::where('item_name', 'GAS LPG 3KG')->first();

        $emptyGas  = MasterItem::where('item_name', 'GAS LPG 3KG KOSONG')->first();

        $response = $this->get('api/stockitems/displaystock/'.$filledGas->id.'/'.$emptyGas->id, 
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

}
