<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\MasterItem;
use App\Models\StockItem;
use App\Models\Transaction;
use Carbon\Carbon;
use Database\Seeders\CategoryItemSeeder;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\MasterItemSeeder;
use Database\Seeders\StockItemSeeder;
use Database\Seeders\TransactionSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class, CategoryItemSeeder::class, MasterItemSeeder::class, StockItemSeeder::class, CustomerSeeder::class]);
        
        $stockItem = StockItem::query()->limit(1)->first();
        $customer = Customer::query()->limit(1)->first();

        $this->post('/api/transactions/' .$stockItem->item_id . '/customer/'.$customer->id, 
        [
            'quantity' => '3',
            'description' => 'Test Description',
            'amount' => 19000,
            'total' => 19000 * 3,
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(201)
        ->assertJson([
            "data" => [
            'quantity' => '3',
            'description' => 'Test Description',
            'amount' => 19000,
            'total' => 19000 * 3,
            ]
        ]);
    }

    public function testCreateSuccess2()
    {

        $this->testCreateSuccess();
        
        $stockItem = StockItem::query()->limit(1)->first();
        $customer = Customer::query()->limit(1)->first();

        $this->post('/api/transactions/' .$stockItem->item_id . '/customer/'.$customer->id, 
        [
            'quantity' => '3',
            'description' => 'Test Description',
            'amount' => 19000,
            'total' => 19000 * 3,
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(201)
        ->assertJson([
            "data" => [
            'quantity' => '3',
            'description' => 'Test Description',
            'amount' => 19000,
            'total' => 19000 * 3,
            ]
        ]);
    }

    public function testUpdateSuccess()
    {
        $this->testCreateSuccess2();
        $transaction = Transaction::query()->limit(1)->first();
        $customer = Customer::where("customer_name", "renan")->first();

        $this->patch('/api/transactions/' .$transaction->id, 
        [ 
            'customer_id' => $customer->id,
            'quantity' => '4',
            'description' => 'Update Description',
            'amount' => 10000,
            'total' => 10000 * 3,
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 200)
        ->assertJson([
           'data' => [
                'customer_id' => $customer->id,
                'quantity' => '4',
                'description' => 'Update Description',
                'amount' => 10000,
                'total' => 10000 * 3,
            ]
        ]);
    }

    public function testgetTodayTransaction()
    {
        $this->seed(
            [UserSeeder::class, CategoryItemSeeder::class, 
            MasterItemSeeder::class, StockItemSeeder::class, 
            CustomerSeeder::class, TransactionSeeder::class,
        ]);
        //2025-01-22 00:00:00

        $response = $this->get('/api/transactions/today', 
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testgetTomorrowTransaction()
    {
        $this->seed(
            [UserSeeder::class, CategoryItemSeeder::class, 
            MasterItemSeeder::class, StockItemSeeder::class, 
            CustomerSeeder::class, TransactionSeeder::class,
        ]);
        //2025-01-22 00:00:00
        $date=Carbon::tomorrow();

        $response = $this->get('/api/transactions/today/'.'2025-01-23',//.$date, 
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }
    
    public function testGetOutsandingTransaction()
    {
        $this->seed(
            [UserSeeder::class, CategoryItemSeeder::class, 
            MasterItemSeeder::class, StockItemSeeder::class, 
            CustomerSeeder::class, TransactionSeeder::class,
        ]);
        //2025-01-22 00:00:00

        $response = $this->get('/api/transactions/outstanding', 
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testGetSalesPerWeek() {
        $this->seed(
            [UserSeeder::class, CategoryItemSeeder::class, 
            MasterItemSeeder::class, StockItemSeeder::class, 
            CustomerSeeder::class, TransactionSeeder::class,
        ]);
        $item = MasterItem::where('item_name', 'test')->first();

        $response = $this->get('api/transactions/salesperweek',
        [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testGetTopCustomer() {
        $this->seed(
            [UserSeeder::class, CategoryItemSeeder::class, 
            MasterItemSeeder::class,  StockItemSeeder::class, 
            CustomerSeeder::class, TransactionSeeder::class,
        ]);
        $item = MasterItem::where('item_name', 'test')->first();

        $response = $this->get('api/transactions/topcustomer',
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 200)
        ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testQueryHasMany()
    {
        $this->testCreateSuccess();
        $transaction = Transaction::query()->first();
        $customer = $transaction->customer;

        self::assertNotNull($customer);
        self::assertEquals("Rizki Zulfianty", $customer->customer_name);
        self::assertEquals("32710918929101", $customer->nik);
    }

}
