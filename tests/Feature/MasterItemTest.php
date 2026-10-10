<?php

namespace Tests\Feature;

use App\Models\AssetOwner;
use App\Models\CategoryItem;
use App\Models\MasterItem;
use Database\Seeders\AssetOwnerSeeder;
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
        $this->seed([UserSeeder::class,
                    CategoryItemSeeder::class,
        ]);
        $category = CategoryItem::query()->first();

        $payload = [
            'item_name'          => 'GAS LPG 3KG ISI',
            'item_type'          => 'ITEM',
            'category_id'        => $category->id,
            'cost_of_goods_sold' => 16000,
            'selling_price'      => 19000,
        ];

        $this->post('/api/masteritems',
                    $payload,
                    ['Authorization' => 'test'])
                    ->assertStatus(201)
                    ->assertJson([
                        "data" => [
                            'item_name'          => 'GAS LPG 3KG ISI',
                            'item_type'          => 'ITEM',
                            'category_id'        => $category->id,
                            'cost_of_goods_sold' => 16000,
                            'selling_price'      => 19000,
                        ]
                    ]);
    }

    public function testCreateAssetSuccess()
    {
        $this->seed([UserSeeder::class,
                    CategoryItemSeeder::class,
                    AssetOwnerSeeder::class,
        ]);
        $category = CategoryItem::query()->first();
        $owner = AssetOwner::query()->first();

        $payload = [
            'item_name'          => 'GAS LPG 3KG KOSONG',
            'item_type'          => 'ASSET',
            'owner_id'           => $owner->id,
            'category_id'        => $category->id,
            'cost_of_goods_sold' => 16000,
            'selling_price'      => 19000,
        ];

        $this->post('/api/masteritems',
                    $payload,
                    ['Authorization' => 'test'])
                    ->assertStatus(201)
                    ->assertJson([
                        "data" => [
                            'item_name'          => 'GAS LPG 3KG KOSONG',
                            'item_type'          => 'ASSET',
                            'owner_id'           => $owner->id,
                            'owner_name'         => $owner->name,
                            'category_id'        => $category->id,
                            'cost_of_goods_sold' => 16000,
                            'selling_price'      => 19000,
                        ]
                    ]);
    }

    public function testCreateSuccessAirMineral()
    {
        $this->testCreateSuccess();
        $category = CategoryItem::query()->first();

        for($i = 0; $i < 10; $i ++) {

            $payload = [
                'item_name'          => 'Air Mineral'.$i,
                'item_type'          => 'ITEM',
                'category_id'        => $category->id,
                'cost_of_goods_sold' => 3000,
                'selling_price'      => 5000,
            ];

            $this->post('/api/masteritems',$payload,
        [
            'Authorization' => 'test'
        ])->assertStatus(201)
        ->assertJson([
            'data' => [
                'item_name'          => 'Air Mineral'.$i,
                'item_type'          => 'ITEM',
                'category_id'        => $category->id,
                'cost_of_goods_sold' => 3000,
                'selling_price'      => 5000,
            ]
        ]);
        }
    }

    public function testCreateItemNameRequired()
    {
        $this->seed([UserSeeder::class,
                     CategoryItemSeeder::class,
                ]);
        $category = CategoryItem::query()->first();

        $payload = [
            'item_name'          => '',
            'item_type'          => 'ITEM',
            'category_id'        => $category->id,
            'cost_of_goods_sold' => 16000,
            'selling_price'      => 19000,
        ];

        $this->post('/api/masteritems', $payload,
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

    public function testCreateOwnerIdNotFound()
    {
        $this->seed([UserSeeder::class,
                     CategoryItemSeeder::class,
                ]);
        $category = CategoryItem::query()->first();

        $payload = [
            'item_name'          => 'Indomie Goreng',
            'item_type'          => 'ITEM',
            'category_id'        => $category->id + 9999,
            'cost_of_goods_sold' => 16000,
            'selling_price'      => 19000,
        ];

        $this->post('/api/masteritems', $payload,
        [
            'Authorization' => 'test'
        ])->assertStatus(404)
        ->assertJson([
            "error" => 'CATEGORY_ITEM_NOT_FOUND'
        ]);
    }

    public function testCreateOwnerIdIsRequiredForAsset()
    {
        $this->seed([UserSeeder::class,
                     CategoryItemSeeder::class,
        ]);
        $category = CategoryItem::query()->first();

        $payload = [
            'item_name'          => 'GAS LPG 3KG KOSONG',
            'item_type'          => 'ASSET',
            'owner_id'           => '',
            'category_id'        => $category->id,
            'cost_of_goods_sold' => 16000,
            'selling_price'      => 19000,
        ];

        $this->post('/api/masteritems',
        $payload,
        [
            'Authorization' => 'test'
        ])->assertStatus(400)
        ->assertJson([
            "errors" => [
                'owner_id' => [
                    'The owner id field is required when item type is ASSET.'
                ]
            ]
        ]);
    }

    public function testItemNameAlreadyExists()
    {
        $this->testCreateSuccess();
        $category = CategoryItem::query()->first();

        $payload = [
            'item_name'          => 'GAS LPG 3KG ISI',
            'item_type'          => 'ITEM',
            'category_id'        => $category->id,
            'cost_of_goods_sold' => 16000,
            'selling_price'      => 19000,
        ];

        $this->post('/api/masteritems', $payload,
        [
            'Authorization' => 'test'
        ])->assertStatus(400)
        ->assertJson([
            "error"=> 'ITEM_NAME_EXISTS'
        ]);
    }

    public function testCreateUnauthorized()
    {
        $this->seed([UserSeeder::class, CategoryItemSeeder::class]);
        $category = CategoryItem::query()->first();

        $payload = [
            'item_name'          => 'GAS LPG 3KG ISI',
            'item_type'          => 'ITEM',
            'category_id'        => $category->id,
            'cost_of_goods_sold' => 16000,
            'selling_price'      => 19000,
        ];

        $this->post('/api/masteritems', $payload,
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
        $this->seed([
            UserSeeder::class,
            CategoryItemSeeder::class,
            MasterItemSeeder::class
        ]);
        $masterItem = MasterItem::query()->first();

        $this->get('/api/masteritems/' .$masterItem->id,
        [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->assertJson([
           'data' => [
                'item_name'          => 'GAS LPG 3KG ISI',
                'cost_of_goods_sold' => 5000,
                'selling_price'      => 10000,
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
                 "error" => "MASTER_ITEM_NOT_FOUND"
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

    public function testGetItemByFlagStatusY()
    {
        $this->testCreateSuccessAirMineral();

        $response = $this->get('api/masteritems/status/'.'Y',
        [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testGetItemByFlagStatusN()
    {
        $this->testInactiveItem();

        $response = $this->get('api/masteritems/status/'.'N',
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

        $payload = [
            'item_name'          => 'Indomie Goreng',
            'item_type'          => 'ITEM',
            'category_id'        => $category->id,
            'cost_of_goods_sold' => 3000,
            'selling_price'      => 3500,
        ];

        $this->put('/api/masteritems/' .$masterItem->id, $payload,
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 200)
        ->assertJson([
           'data' => [
                'item_name'          => 'Indomie Goreng',
                'item_type'          => 'ITEM',
                'category_id'        => $category->id,
                'cost_of_goods_sold' => 3000,
                'selling_price'      => 3500,
            ]
        ]);
    }

    public function testUpdateValidationError()
    {
        $this->seed([
            UserSeeder::class,
            CategoryItemSeeder::class,
            MasterItemSeeder::class
        ]);
        $masterItem = MasterItem::query()->limit(1)->first();
        $category = CategoryItem::query()->first();

        $payload = [
            'item_name'          => '',
            'item_code'          => 'M001',
            'category_id'        => $category->id,
            'cost_of_goods_sold' => 3000,
            'selling_price'      => 3500,
        ];

        $this->put('/api/masteritems/' .$masterItem->id, $payload,
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
        $this->testCreateSuccessAirMineral();
        $masterItem = MasterItem::query()->limit(1)->first();
        $category = CategoryItem::query()->first();

        $payload = [
            'item_name'          => 'Air Mineral0',
            'item_type'          => 'ITEM',
            'category_id'        => $category->id,
            'cost_of_goods_sold' => 3000,
            'selling_price'      => 3500,
        ];

        $this->put('/api/masteritems/' .$masterItem->id, $payload,
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 400)
        ->assertJson([
            "error"=> "ITEM_NAME_EXISTS"
        ]);
    }

     public function testGetAllSuccess()
    {

         $this->testCreateSuccessAirMineral();
         $this->seed([StockItemSeeder::class]);

        $response = $this->get('/api/masteritems/all', [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }


    public function testGetItemByItemType()
    {
       $this->testCreateSuccessAirMineral();

        $response = $this->get('/api/masteritems/itemtype/'. 'ASSET', [
            'Authorization' => 'test'
        ])->assertStatus(status: 200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testGetItemGasIsiSuccess() {
        $this->testCreateSuccess();

        $this->get('/api/masteritems/itemGasIsi',
        [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->assertJson([
            'data' => [
                'item_name'     => 'GAS LPG 3KG ISI',
                'cost_of_goods_sold' => 16000,
                'selling_price'      => 19000,
            ]
        ]);
    }

    public function testInactiveItem()
    {
        $this->seed([
            UserSeeder::class,
            CategoryItemSeeder::class,
            MasterItemSearchSeeder::class
        ]);

        $masterItem = MasterItem::query()->first();
        $response = $this->patch("/api/masteritems/{$masterItem->id}/inactive",[],
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
