<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Debt;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\DebtSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class DebtTest extends TestCase
{
    public function testCreateNewDebtSuccess()
    {
        $this->seed( [UserSeeder::class, CustomerSeeder::class] );
        $customer = Customer::where("customer_name", "test")->first();

        $response = $this->post('/api/debts/'. $customer->id, [
            'description' => 'khannedy',
            'total' => 100000,
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(201)
        ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
        // ->assertStatus(201)
        // ->assertJson([
        //     "data" =>[
        //         'description' => 'khannedy',
        //         'total' => 100000,
        //         ]
        //     ]);
    }

    public function testCreateNewDebtAmountEmpty()
    {
        $this->seed( [UserSeeder::class, CustomerSeeder::class] );
        $customer = Customer::where("customer_name", "test")->first();

        $this->post('/api/debts/'. $customer->id, [
            'description' => 'khannedy',
            'amount_pay' => 0,
            'total' => 0,
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(400)
        ->assertJson([
            "errors" => 'One of the amount field must be filled',
            ]);
    }
    public function testGetDebtByCustomer() {
        $this->seed( [UserSeeder::class, CustomerSeeder::class, DebtSeeder::class] );
        $customer = Customer::where("customer_name", "test")->first();

        $response = $this->get('api/debts/customer/'. $customer->id,
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 200)
        ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testGetDebtSummarySuccess() {
        $this->seed( [UserSeeder::class, CustomerSeeder::class, DebtSeeder::class] );

        $response = $this->get('api/debts/summary',
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 200)
        ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testGetDebtOutstandingSuccess() {
        $this->seed( [UserSeeder::class, CustomerSeeder::class, DebtSeeder::class] );

        $response = $this->get('api/debts/outstanding',
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 200)
        ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testUpdateSuccess()
    {
        $this->testCreateNewDebtSuccess();

        $debt = Debt::query()->limit(1)->first();
        $customer = Customer::where("customer_name", "renan")->first();

        $this->patch('/api/debts/' .$debt->id, [
            'customer_id' => $customer->id,
            'description' => 'Test Update',
            'amount_pay' => 500,
            'total' => 9812,
        ], [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->assertJson([
            'data' => [
                'customer_id' => $customer->id,
                'customer_name' => 'renan',
                'description' => 'Test Update',
                'amount_pay' => 500,
                'total' => 9812,
            ]
            ]);
    }

    public function testUpdateSuccessWithNoCustomer()
    {
        $this->testCreateNewDebtSuccess();

        $debt = Debt::query()->limit(1)->first();
        // $customer = Customer::where("customer_name", "renan")->first();

        $this->patch('/api/debts/' .$debt->id, 
        [
            // 'customer_id' => $customer->id,
            'description' => 'Test Update',
            'amount_pay' => 500,
            'total' => 9812,
        ], [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->assertJson([
            'data' => [
                // 'customer_id' => $customer->id,
                // 'customer_name' => 'renan',
                'description' => 'Test Update',
                'amount_pay' => 500,
                'total' => 9812,
            ]
            ]);
    }
}
