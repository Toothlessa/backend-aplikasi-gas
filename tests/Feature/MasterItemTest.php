<?php

namespace Tests\Feature;

use App\Models\CategoryItem;
use App\Models\MasterItem;
use Database\Seeders\CategoryItemSeeder;
use Database\Seeders\MasterItemSearchSeeder;
use Database\Seeders\MasterItemSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class MasterItemTest extends TestCase
{
    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class, CategoryItemSeeder::class]);
        $category = CategoryItem::query()->first();

        $this->post('/api/masteritems', [
            'item_name' => 'Gas LPG 3 Kg',
            'item_type' => 'ASSET',
            'category_id' => $category->id,
            'cost_of_goods_sold' => 16000,
            'selling_price' => 19000,
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(201)
        ->assertJson([
            "data" => [
            'item_name' => 'Gas LPG 3 Kg',
            'item_code' => 'TE01',
            'item_type' => 'ASSET',
            'category' => 'Bahan Pokok',
            'cost_of_goods_sold' => 16000,
            'selling_price' => 19000,
            ]
        ]);
    }

    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class]);
        $category = CategoryItem::query()->first();

        $this->post('/api/masteritems', [
            'item_name' => '',
            'category' => 'Bahan Pokok',
            'cost_of_goods_sold' => 16000,
            'selling_price' => 19000,
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(400)
        ->assertJson([
            "errors" => [
                'item_name' => [
                    'The item name field is required.'
                ]
            ]
        ]);
    }
    public function testItemNameAlreadyExists()
    {
        $this->testCreateSuccess();
        $category = CategoryItem::query()->first();

        $this->post('/api/masteritems', [
            'item_name' => 'Gas LPG 3 Kg',
            'item_code' => 'G01',
            'category_id' => $category->id,
            'cost_of_goods_sold' => 16000,
            'selling_price' => 19000,
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(400)
        ->assertJson([
            "errors"=> 'ITEM_NAME_IS_REGISTERED'
        ]);
    }

    public function testCreateUnauthorized()
    {
        $this->seed([UserSeeder::class, CategoryItemSeeder::class]);
        $category = CategoryItem::query()->first();

        $this->post('/api/masteritems', [
            'item_name' => 'Gas LPG 3 Kg',
            'item_code' => 'G01',
            'category_id' => $category->id,
            'cost_of_goods_sold' => 16000,
            'selling_price' => 19000,
        ],
        [
            'Authorization' => 'salah'
        ])->assertStatus(401)
        ->assertJson([
            "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
        ]);
    }

    public function testGetItemSuccess()
    {
        $this->seed([UserSeeder::class, CategoryItemSeeder::class, MasterItemSeeder::class]);
        $masterItem = MasterItem::where('item_name', 'GAS LPG 3KG')->first();

        $this->get('/api/masteritems/' .$masterItem->id, 
        [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->assertJson([
           'data' => [
                'item_name' => 'GAS LPG 3KG',
                'item_code' => 'test00',
                'cost_of_goods_sold' => 5000,
                'selling_price' => 10000,
                ]
        ]);
    }
    public function testGetItemNotFound()
    {
        $this->seed([UserSeeder::class, CategoryItemSeeder::class, MasterItemSeeder::class]);
        $masterItem = MasterItem::query()->limit(1)->first();

        $this->get('/api/masteritems/' .($masterItem->id + 100), 
        [
            'Authorization' => 'test'
        ])->assertStatus(404)
        ->assertJson([
                 "errors" => "NOT_FOUND"
        ]);
    }

    public function testGetUnauthorized()
    {
        $this->seed([UserSeeder::class, CategoryItemSeeder::class, MasterItemSeeder::class]);
        $masterItem = MasterItem::query()->limit(1)->first();

        $this->get('/api/masteritems/' .$masterItem->id, 
        [
            'Authorization' => 'salah'
        ])->assertStatus(401)
        ->assertJson([
            "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
        ]);
    }

    public function testUpdateItemSuccess()
    {
        $this->seed([UserSeeder::class, CategoryItemSeeder::class, MasterItemSeeder::class]);
        $masterItem = MasterItem::query()->limit(1)->first();
        $category = CategoryItem::query()->first();

        $this->put('/api/masteritems/' .$masterItem->id, 
        [ 'item_name' => 'Indomie Goreng',
                'item_type' => 'RT',
                'item_code' => 'AAP001',
                'category_id' => $category->id,
                'cost_of_goods_sold' => 3000,
                'selling_price' => 3500,
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 200)
        ->assertJson([
           'data' => [
                'item_name' => 'Indomie Goreng',
                'item_type' => 'RT',
                'item_code' => 'AAP001',
                'cost_of_goods_sold' => 3000,
                'selling_price' => 3500,
            ]
        ]);
    }

    public function testUpdateValidationError()
    {
        $this->seed([UserSeeder::class, CategoryItemSeeder::class, MasterItemSeeder::class]);
        $masterItem = MasterItem::query()->limit(1)->first();
        $category = CategoryItem::query()->first();

        $this->put('/api/masteritems/' .$masterItem->id, 
        [ 'item_name' => '',
                'item_code' => 'M001',
                'category_id' => $category->id,
                'cost_of_goods_sold' => 3000,
                'selling_price' => 3500,
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 400)
        ->assertJson([
           'errors' => [
                    'item_name' => [
                        'The item name field is required.'
                    ]
                ]
        ]);
    }

    public function testUpdateItemAlreadyExists()
    {
        $this->seed([UserSeeder::class, CategoryItemSeeder::class, MasterItemSeeder::class]);
        $masterItem = MasterItem::query()->limit(1)->first();
        $category = CategoryItem::query()->first();

        $this->put('/api/masteritems/' .$masterItem->id, 
        [ 'item_name' => 'test1',
                'item_type' => 'RT',
                'category_id' => $category->id,
                'cost_of_goods_sold' => 3000,
                'selling_price' => 3500,
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 400)
        ->assertJson([
            "errors"=> "ITEM_NAME_IS_REGISTERED"
        ]);
    }

    public function testUpdateItemCodeAlreadyExists()
    {
        $this->seed([UserSeeder::class, CategoryItemSeeder::class, MasterItemSeeder::class]);
        $masterItem = MasterItem::query()->limit(1)->first();
        $category = CategoryItem::query()->first();

        $this->put('/api/masteritems/' .$masterItem->id, 
        [ 'item_name' => 'Indomie Goreng',
                'item_type' => 'RT',
                'item_code' => 'test002',
                'category_id' => $category->id,
                'cost_of_goods_sold' => 3000,
                'selling_price' => 3500,
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 400)
        ->assertJson([
          "errors"=> "ITEM_CODE_IS_REGISTERED"
        ]);
    }

    public function testDeleteItemSuccess()
    {
        $this->seed([UserSeeder::class, CategoryItemSeeder::class, MasterItemSeeder::class]);
        $masterItem = MasterItem::query()->limit(1)->first();
        $category = CategoryItem::query()->first();

        $this->delete('/api/masteritems/' .$masterItem->id, [],
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 200)
        ->assertJson([
          'data'=> true
        ]);
    }
    public function testDeleteItemNotFound()
    {
        $this->seed([UserSeeder::class, CategoryItemSeeder::class, MasterItemSeeder::class]);
        $masterItem = MasterItem::query()->limit(1)->first();

        $this->delete('/api/masteritems/' .($masterItem->id + 101), [],
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 404)
        ->assertJson([
            'errors' => "NOT_FOUND"
        ]);
    }

    public function testDeleteItemUnauthorized()
    {
        $this->seed([UserSeeder::class, CategoryItemSeeder::class, MasterItemSeeder::class]);
        $masterItem = MasterItem::query()->limit(1)->first();

        $this->delete('/api/masteritems/' .$masterItem->id, [],
        [
            'Authorization' => 'salah'
        ])->assertStatus(status: 401)
        ->assertJson([
            'errors' => [
                "message" => [
                    "unauthorized"
                ]
            ]
        ]);
    }

    public function testSearchByItemName()
    {
        $this->seed([UserSeeder::class, CategoryItemSeeder::class, MasterItemSearchSeeder::class]);

        $response = $this->get('/api/masteritems?item_name=test', [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(9, count($response['data']));
        self::assertEquals(9, $response['meta']['total']);
    }

    public function testSearchByItemCode()
    {
        $this->seed([UserSeeder::class, CategoryItemSeeder::class, MasterItemSearchSeeder::class]);

        $response = $this->get('/api/masteritems?item_code=M', [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(0, count($response['data']));
        self::assertEquals(0, $response['meta']['total']);
    }

    // public function testSearchByItemCategory()
    // {
    //     $this->seed([UserSeeder::class, CategoryItemSeeder::class, MasterItemSearchSeeder::class]);

    //     $response = $this->get('/api/masteritems?category=Makan', [
    //         'Authorization' => 'test'
    //     ])->assertStatus(500)
    //     ->Json();

    //     Log::info(json_encode($response, JSON_PRETTY_PRINT));

    //     self::assertEquals(0, count($response['data']));
    //     self::assertEquals(0, $response['meta']['total']);
    // }

    public function testSearchNotFound()
    {
        $this->seed([UserSeeder::class, CategoryItemSeeder::class, MasterItemSearchSeeder::class]);

        $response = $this->get('/api/masteritems?category=Minuman', [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(10, $response['meta']['total']);
    }

    public function testSearchPageSize()
    {
        $this->seed([UserSeeder::class, CategoryItemSeeder::class, MasterItemSearchSeeder::class]);

        $response = $this->get('/api/masteritems?page=1&size=5', [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(5, count($response['data']));
        self::assertEquals(10, $response['meta']['total']);
        self::assertEquals(1, $response['meta']['current_page']);
    }

    public function testGenerateItemCodeSuccess()
    {
        $this->testSearchPageSize();
        $category = CategoryItem::query()->first();

        $this->post('/api/masteritems', [
            'item_name' => 'Gas LPG 12 Kg',
            'category_id' => $category->id,
            'cost_of_goods_sold' => 205000,
            'selling_price' => 215000,
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 201)
        ->assertJson([
            "data" => [
            'item_name' => 'Gas LPG 12 Kg',
            'item_code' => 'BP09',
            'cost_of_goods_sold' => 205000,
            'selling_price' => 215000,
            ]
        ]);
    }

    public function testGetAllMasterItem()
    {
        $this->seed([UserSeeder::class, CategoryItemSeeder::class, MasterItemSearchSeeder::class]);

        // $itemType = null;

        $response = $this->get('/api/masteritems/all', [
            'Authorization' => 'test'
        ])->assertStatus(status: 200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testGetItemByItemType()
    {
        $this->seed([UserSeeder::class, CategoryItemSeeder::class, MasterItemSearchSeeder::class]);

        $itemType = 'ITEM';

        $response = $this->get('/api/masteritems/itemtype/'. $itemType, [
            'Authorization' => 'test'
        ])->assertStatus(status: 200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }


    public function testInactiveItem()
    {
        $this->seed([UserSeeder::class, CategoryItemSeeder::class, MasterItemSearchSeeder::class]);

        $masterItem = MasterItem::query()->first();
        $response = $this->patch('/api/masteritems/inactive/'.$masterItem->id,[], 
[
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->assertJson([
            'data' => [
                'active_flag' => 'N',
            ]
        ]);

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }
}
    

