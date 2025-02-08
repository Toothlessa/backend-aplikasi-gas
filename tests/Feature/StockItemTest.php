<?php

namespace Tests\Feature;

use App\Models\MasterItem;
use App\Models\StockItem;
use Carbon\Carbon;
use Database\Seeders\MasterItemSeeder;
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
        $this->seed([UserSeeder::class, MasterItemSeeder::class]);
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
        $this->testInputStockSuccess();
        $stockInput = StockItem::query()->limit(1)->first();
        $masterItem = MasterItem::where("item_name", "test1")->orderByDesc("id")->first();

        $this->put('/api/stockitems/' .$stockInput->id, 
        [ 
            'item_id' => $masterItem->id,
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
        $masterItem = MasterItem::where("item_name", "test")->first();

        $response = $this->get('api/stockitems/detailstock/'.$masterItem->id, 
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testGetDisplayStock() {
        $this->testInputStockSuccess();
        $stockItem = StockItem::query()->first();
        $yesterdayStock = StockItem::where('item_id', $stockItem->item_id)
                                ->where('created_at', Carbon::yesterday())
                                ->sum('stock');
        $runStock = StockItem::where('item_id', $stockItem->item_id)->sum('stock');
        $emptyGas = 560 - $runStock; 

        $arrName=array("yesterday_stock","running_stock","emptyGasOwned");
        $arrValue=array($yesterdayStock, $runStock, $emptyGas);
        $displayStock=array_combine($arrName,$arrValue);

        assertNotNull($runStock);
        assertEquals($runStock, 100);
        assertEquals($emptyGas, 460);
        assertEquals($yesterdayStock, 0);
        // assertJson($displayStock, "a");
        Log::info(json_encode($displayStock, JSON_PRETTY_PRINT));
    }

}
