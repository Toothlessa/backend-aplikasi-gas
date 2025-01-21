<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\StockItem;
use App\Models\Transaction;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\MasterItemSeeder;
use Database\Seeders\StockItemSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class, MasterItemSeeder::class, StockItemSeeder::class, CustomerSeeder::class]);
        
        $stockItem = StockItem::query()->limit(1)->first();
        $customer = Customer::query()->limit(1)->first();

        $this->post('/api/transaction/' .$stockItem->item_id . '/'.$customer->id, 
        [
            'quantity' => '3',
            'description' => 'Test Description',
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(201)
        ->assertJson([
            "data" => [
            'quantity' => '3',
            'description' => 'Test Description',
            ]
        ]);
    }

    public function testCreateSuccess2()
    {

        $this->testCreateSuccess();
        
        $stockItem = StockItem::query()->limit(1)->first();
        $customer = Customer::query()->limit(1)->first();

        $this->post('/api/transaction/' .$stockItem->item_id . '/'.$customer->id, 
        [
            'quantity' => '3',
            'description' => 'Test Description',
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(201)
        ->assertJson([
            "data" => [
            'quantity' => '3',
            'description' => 'Test Description',
            ]
        ]);
    }

    public function testgetTodayTransaction()
    {
        $this->testCreateSuccess2();

        $response = $this->get('/api/transaction/today', 
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testUpdateSuccess()
    {
        $this->testCreateSuccess2();
        $transaction = Transaction::query()->limit(1)->first();

        $this->patch('/api/transaction/' .$transaction->id, 
        [ 
            'quantity' => '4',
            'amount' => '10000',
            'description' => 'Update Description',
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 200)
        ->assertJson([
           'data' => [
                'quantity' => '4',
                'amount' => '10000',
                'description' => 'Update Description',
            ]
        ]);
    }

    public function testQueryHasMany()
    {
        $this->testCreateSuccess();
        $transaction = Transaction::query()->limit(1)->first();
        $customer = $transaction->customer;

        self::assertNotNull($customer);
        self::assertEquals("test", $customer->customer_name);
    }
}
