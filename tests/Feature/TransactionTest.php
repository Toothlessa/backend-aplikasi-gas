<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\MasterItem;
use App\Models\Transaction;
use Carbon\Carbon;
use Database\Seeders\CategoryItemSeeder;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\MasterItemSeeder;
use Database\Seeders\StockItemSeeder;
use Database\Seeders\TransactionSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    public function testCreateSuccess()
    {
        $this->seed([
                    UserSeeder::class, 
                    CategoryItemSeeder::class, 
                    MasterItemSeeder::class, 
                    StockItemSeeder::class, 
                    CustomerSeeder::class
                ]);
        
        $masterItem = MasterItem::query()->first();
        $customer   = Customer::query()->first();

        $payload =  [
            'item_id'       => $masterItem->id,
            'customer_id'   => $customer->id,
            'quantity'      => '3',
            'description'   => 'Test Description',
            'amount'        => 19000,
            'payment_method'=> 'CASH',
            'paid_amount'   => 19000,
        ];

        $this->post('/api/transactions/', $payload,
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 201)
        ->assertJson([
            "data" => [
                'nik'           => $customer->nik,
                'customer_name' => $customer->customer_name,
                'item_name'     => $masterItem->item_name,
                'quantity'      => '3',
                'description'   => 'Test Description',
                'amount'        => 19000,
                'total'         => 19000 * 3,
                'payment_method'=> 'CASH',
                'paid_amount'   => 19000 * 3,
            ]
        ]);
    }

    public function testCreateTransactionPartialPayment(){
        $this->seed([
                    UserSeeder::class, 
                    CategoryItemSeeder::class, 
                    MasterItemSeeder::class, 
                    StockItemSeeder::class, 
                    CustomerSeeder::class
                ]);

        $masterItem = MasterItem::query()->first();
        $customer = Customer::query()->first();

        $payload = [
            'item_id'       => $masterItem->id,
            'customer_id'   => $customer->id,
            'quantity'      => '2',
            'description'   => 'Test Partial',
            'amount'        => 19000,
            'payment_method'=> 'PARTIAL',
            'paid_amount'   => 5000,
        ];

        $this->post('/api/transactions', $payload,
        [
            'Authorization' => 'test'
        ])->assertStatus(201)
        ->assertJson([
            "data" => [
                'nik'           => $customer->nik,
                'customer_name' => $customer->customer_name,
                'item_name'     => $masterItem->item_name,
                'quantity'      => '2',
                'description'   => 'Test Partial',
                'amount'        => 19000,
                'total'         => 19000 * 2,
                'payment_method'=> 'PARTIAL',
                'paid_amount'   => 5000,
            ]
        ]);
    }

    public function testCreateSuccessNewTransaction()
    {
        $this->seed([
                    UserSeeder::class, 
                    CategoryItemSeeder::class, 
                    MasterItemSeeder::class, 
                    StockItemSeeder::class, 
                    CustomerSeeder::class
                ]);
        
        $masterItem = MasterItem::query()->first();
        $customer   = Customer::query()->first();

        $payload =  [
            'item_id'       => $masterItem->id,
            'customer_id'   => $customer->id,
            'quantity'      => '1',
            'description'   => 'Create New Transaction',
            'amount'        => 19000,
            'payment_method'=> 'CASH',
            'paid_amount'   => 19000,
        ];
        $this->post('/api/transactions', $payload,
        [
            'Authorization' => 'test'
        ])->assertStatus(201)
        ->assertJson([
            "data" => [
                'nik'           => $customer->nik,
                'customer_name' => $customer->customer_name,
                'item_name'     => $masterItem->item_name,
                'quantity'      => '1',
                'description'   => 'Create New Transaction',
                'amount'        => 19000,
                'total'         => 19000 * 1,
                'payment_method'=> 'CASH',
                'paid_amount'   => 19000,
            ]
        ]);
    }

    public function testCreateQuantityMinus(){
        $this->seed([
                    UserSeeder::class, 
                    CategoryItemSeeder::class, 
                    MasterItemSeeder::class, 
                    StockItemSeeder::class, 
                    CustomerSeeder::class
                ]);

        $masterItem = MasterItem::query()->first();
        $customer = Customer::query()->first();

        $payload =  [
            'item_id'       => $masterItem->id,
            'customer_id'   => $customer->id,
            'quantity'      => '-1',
            'description'   => 'Create New Transaction',
            'amount'        => 19000,
            'payment_method'=> 'CASH',
            'paid_amount'   => 19000,
        ];

        $this->postJson(
            '/api/transactions',
            $payload,
            ['Authorization' => 'test']
        )
        ->assertStatus(400)
        ->assertJson([
            'errors' => [ 
                'quantity' => [
                    'The quantity field must be at least 1.'
                ]
            ]
            ]);
    }

    public function testCreateAmountMinus(){
        $this->seed([
                    UserSeeder::class, 
                    CategoryItemSeeder::class, 
                    MasterItemSeeder::class, 
                    StockItemSeeder::class, 
                    CustomerSeeder::class
                ]);
        
        $masterItem = MasterItem::query()->first();
        $customer = Customer::query()->first();

        $payload =  [
            'item_id'       => $masterItem->id,
            'customer_id'   => $customer->id,
            'quantity'      => '1',
            'description'   => 'Create New Transaction',
            'amount'        => -19000,
            'payment_method'=> 'CASH',
            'paid_amount'   => 19000,
        ];

        $this->postJson(
            '/api/transactions',
            $payload,
            ['Authorization' => 'test']
        )
        ->assertStatus(400)
        ->assertJson([
            'errors' => [ 
                'amount' => [
                    'The amount field must be at least 0.'
                ]
            ]
            ]);
    }
    public function testUpdateSuccess(){
       $this->seed([
                    UserSeeder::class, 
                    CategoryItemSeeder::class, 
                    MasterItemSeeder::class, 
                    StockItemSeeder::class, 
                    CustomerSeeder::class,
                    TransactionSeeder::class,
                ]);

        $transaction    = Transaction::first();
        $customer       = Customer::where('customer_name', 'renan')->first();
        $masterItem     = MasterItem::query()->first();

        $payload =  [
            'item_id'       => $masterItem->id,
            'customer_id'   => $customer->id,
            'quantity'      => '3',
            'description'   => 'Test Update Description',
            'amount'        => 19000,
            'payment_method'=> 'CASH',
            'paid_amount'   => 19000,
        ];

        $this->patchJson(
            "/api/transactions/{$transaction->id}",
            $payload,
            ['Authorization' => 'test']
        )
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'nik'           => $customer->nik,
                'customer_name' => $customer->customer_name,
                'item_name'     => $masterItem->item_name,
                'quantity'      => '3',
                'description'   => 'Test Update Description',
                'amount'        => 19000,
                'total'         => 19000 * 3,
                'payment_method'=> 'CASH',
                'paid_amount'   => 19000 * 3,
            ]
        ]);
    }

    public function testUpdateQuantityMinus(){
        $this->testCreateSuccess2();

        $transaction = Transaction::first();
        $customer = Customer::where('customer_name', 'renan')->first();

        $payload = [
            'customer_id' => $customer->id,
            'description' => 'Update Description',
            'amount' => 10000,
            'quantity' => -10,
        ];

        $this->patchJson(
            "/api/transactions/{$transaction->id}",
            $payload,
            ['Authorization' => 'test']
        )
        ->assertStatus(400)
        ->assertJson([
            'errors' => [ 
                'quantity' => [
                    'The quantity field must be at least 1.'
                ]
            ]
            ]);
    }

    public function testUpdateAmountMinus(){
        $this->testCreateSuccess2();

        $transaction = Transaction::first();
        $customer = Customer::where('customer_name', 'renan')->first();

        $payload = [
            'customer_id' => $customer->id,
            'description' => 'Update Description',
            'amount' => -10000,
            'quantity' => 10,
        ];

        $this->patchJson(
            "/api/transactions/".$transaction->id,
            $payload,
            ['Authorization' => 'test']
        )
        ->assertStatus(400)
        ->assertJson([
            'errors' => [ 
                'amount' => [
                    'The amount field must be at least 0.'
                ]
            ]
            ]);
    }

    public function testgetTodayTransaction()
    {
        $this->seed([
            UserSeeder::class, 
            CategoryItemSeeder::class, 
            MasterItemSeeder::class, 
            CustomerSeeder::class, 
            TransactionSeeder::class,
        ]);

        $response = $this->get('/api/transactions/date/', 
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testgetTomorrowTransaction()
    {
        $this->seed([
            UserSeeder::class, 
            CategoryItemSeeder::class, 
            MasterItemSeeder::class,
            CustomerSeeder::class, 
            TransactionSeeder::class,
        ]);

         $date = Carbon::tomorrow()->toDateString(); // YYYY-MM-DD


        $response = $this->get('/api/transactions/date/'.$date,
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testgetYesterdayTransaction()
    {
        $this->seed([
            UserSeeder::class, 
            CategoryItemSeeder::class, 
            MasterItemSeeder::class,
            CustomerSeeder::class, 
            TransactionSeeder::class,
        ]);

         $date = Carbon::yesterday()->toDateString(); // YYYY-MM-DD

        $response = 
            $this->get('/api/transactions/date/'.$date,[
                'Authorization' => 'test'
            ])->assertStatus(status: 200)
            ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }
    
    public function testGetOutsandingTransaction()
    {
        $this->seed([
            UserSeeder::class, 
            CategoryItemSeeder::class, 
            MasterItemSeeder::class,
            CustomerSeeder::class, 
            TransactionSeeder::class,
        ]);
        //2025-01-22 00:00:00

        $response = $this->get('/api/transactions/outstanding', 
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testGetDailySale() {
        $this->seed([
            UserSeeder::class, 
            CategoryItemSeeder::class, 
            MasterItemSeeder::class, 
            CustomerSeeder::class, 
            TransactionSeeder::class,
        ]);

        $response = $this->get('api/transactions/chart/daily-sale',
        [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testGetTopCustomer() {
        $this->seed([
            UserSeeder::class, 
            CategoryItemSeeder::class, 
            MasterItemSeeder::class, 
            CustomerSeeder::class, 
            TransactionSeeder::class,
        ]);

        $response = $this->get('api/transactions/chart/top-customer',
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
        self::assertEquals("test", $customer->customer_name);
        self::assertEquals("3271981923812912", $customer->nik);
    }

}
