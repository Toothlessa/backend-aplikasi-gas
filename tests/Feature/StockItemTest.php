<?php

namespace Tests\Feature;

use App\Models\MasterItem;
use App\Models\StockItem;
use Database\Seeders\MasterItemSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class StockItemTest extends TestCase
{
    
    public function testInputStockSuccess()
    {
        // $this->seed([UserSeeder::class, MasterItemSeeder::class]);
        $masterItem = MasterItem::query()->limit(1)->first();

        $this->post('/api/stockitems/' .($masterItem->id), [
            'stock' => 100,
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 201)
        ->assertJson([
            'data' => [
                'stock' => 100,
            ]
        ]);
    }

    public function testUpdateStockSuccess()
    {
        // $this->testInputStockSuccess();
        $stockInput = StockItem::query()->limit(1)->first();

        $this->put('/api/stockitems/' .$stockInput->id, 
        [ 
            'stock' => '560'
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 200)
        ->assertJson([
           'data' => [
            'stock' => '560'
            ]
        ]);
    }

    public function testGetCurrenStock()
    {

        $response = $this->get('api/stockitems/currentstock', 
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }
}
