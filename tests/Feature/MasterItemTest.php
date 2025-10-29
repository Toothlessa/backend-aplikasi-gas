<?php

namespace Tests\Feature;

use App\Models\CategoryItem;
use App\Models\MasterItem;
use Database\Seeders\CategoryItemSeeder;
use Database\Seeders\MasterItemSearchSeeder;
use Database\Seeders\MasterItemSeeder;
use Database\Seeders\StockItemSeeder;
use Database\Seeders\UserSeeder;
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
            'item_type' => 'ASSET',
            'category_id' => $category->id,
            'cost_of_goods_sold' => 16000,
            'selling_price' => 19000,
            ]
        ]);
    }

    public function testCreateSuccess1()
    {
        $this->testCreateSuccess();
        $category = CategoryItem::query()->first();

        for($i = 0; $i < 10; $i ++) {
            $this->post('/api/masteritems', [
            'item_name' => 'Air Mineral'.$i,
            'item_type' => 'ITEM',
            'category_id' => $category->id,
            'cost_of_goods_sold' => 3000,
            'selling_price' => 5000,
            ],
        [
            'Authorization' => 'test'
        ])->assertStatus(201)
        ->assertJson([
            'data' => [
                'item_name' => 'Air Mineral'.$i,
                'item_type' => 'ITEM',
                'category_id' => $category->id,
                'cost_of_goods_sold' => 3000,
                'selling_price' => 5000,   
            ]
        ]);
        }
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
            'category_id' => $category->id,
            'cost_of_goods_sold' => 16000,
            'selling_price' => 19000,
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(400)
        ->assertJson([
            "errors"=> 'ITEM_NAME_EXISTS'
        ]);
    }

    public function testCreateUnauthorized()
    {
        $this->seed([UserSeeder::class, CategoryItemSeeder::class]);
        $category = CategoryItem::query()->first();

        $this->post('/api/masteritems', [
            'item_name' => 'Gas LPG 3 Kg',
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
        $masterItem = MasterItem::query()->first();//where('item_name', 'Gas LPG 3 Kg')->first();

        $this->get('/api/masteritems/' .$masterItem->id, 
        [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->assertJson([
           'data' => [
                'item_name' => 'GAS LPG 3KG',
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
                 "errors" => "MASTER_ITEM_NOT_FOUND"
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

    public function testGetItemByFlagStatus()
    {
        $this->testCreateSuccess1();

        $response = $this->get('api/masteritems/'.'Y',
        [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testGetItemByFlagStatusN()
    {
        $this->testInactiveItem();

        $response = $this->get('api/masteritems/'.'N',
        [
            'Authorization' => 'test'
        ])->assertStatus(200);

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testUpdateItemSuccess()
    {
        $this->testCreateSuccess();

        $masterItem = MasterItem::query()->first();
        $category   = CategoryItem::where('prefix', 'AT')->first();

        $this->put('/api/masteritems/' .$masterItem->id, 
        [ 'item_name' => 'Indomie Goreng',
                'item_type' => 'ITEM',
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
                'item_type' => 'ITEM',
                'category_id' => $category->id,
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
        $this->testCreateSuccess1();
        $masterItem = MasterItem::query()->limit(1)->first();
        $category = CategoryItem::query()->first();

        $this->put('/api/masteritems/' .$masterItem->id, 
        [ 'item_name' => 'Air Mineral0',
                'item_type' => 'RT',
                'category_id' => $category->id,
                'cost_of_goods_sold' => 3000,
                'selling_price' => 3500,
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 400)
        ->assertJson([
            "errors"=> "ITEM_NAME_EXISTS"
        ]);
    }

     public function testGetAllSuccess()
    {

         $this->testCreateSuccess1();
         $this->seed([StockItemSeeder::class]);

        $response = $this->get('/api/masteritems/all', [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }


    public function testGetItemByItemType()
    {
       $this->testCreateSuccess1();

        $response = $this->get('/api/masteritems/itemtype/'. 'ASSET', [
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
    

