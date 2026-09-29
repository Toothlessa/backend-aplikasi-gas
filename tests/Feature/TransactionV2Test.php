<?php

namespace Tests\Feature;

use App\Models\Transaction;
use Tests\TestCase;
use App\Models\MasterItem;
use App\Models\Customer;
use Database\Seeders\UserSeeder;
use Database\Seeders\CategoryItemSeeder;
use Database\Seeders\MasterItemSeeder;
use Database\Seeders\StockItemSeeder;
use Database\Seeders\CustomerSeeder;
// use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\DatabaseTransactions; 

class TransactionV2Test extends TestCase
{
//    use RefreshDatabase;

    // ═══════════════════════════════════════
    // Properties — reusable di semua test
    // ═══════════════════════════════════════
    protected MasterItem    $masterItem;
    protected Customer      $customer;
    protected Transaction   $transaction;

    // ═══════════════════════════════════════
    // Otomatis dipanggil sebelum setiap test
    // ═══════════════════════════════════════
    protected function setUp(): void
    {
        parent::setUp();
        $this->seedDatabase();
        $this->setSharedData();
    }

    // ═══════════════════════════════════════
    // Private Methods
    // ═══════════════════════════════════════
    private function seedDatabase(): void
    {
        $this->seed([
            UserSeeder::class,
            CategoryItemSeeder::class,
            MasterItemSeeder::class,
            StockItemSeeder::class,
            CustomerSeeder::class,
        ]);
    }

    private function setSharedData(): void
    {
        $this->masterItem = MasterItem::query()->first();
        $this->customer   = Customer::query()->first();
    }

    private function transactionPayload(array $override = []): array
    {
        return array_merge([
            'item_id'        => $this->masterItem->id,
            'customer_id'    => $this->customer->id,
            'quantity'       => '3',
            'description'    => 'Test Description V2',
            'amount'         => 19000,
            'payment_method' => 'CASH',
            'paid_amount'    => 19000,
        ], $override);
    }

    private function transactionUpdatePayload(array $override = []): array
    {
        return array_merge([
            'id'             => $this->transaction->id,
            'item_id'        => $this->masterItem->id,
            'customer_id'    => $this->customer->id,
            'quantity'       => '5',
            'description'    => 'Test Update Partial Payment Description V2',
            'amount'         => 19000,
            'payment_method' => 'PARTIAL',
            'paid_amount'    => 3000,
        ], $override);
    }

    private function authHeader(): array
    {
        return ['Authorization' => 'test'];
    }

    // ═══════════════════════════════════════
    // Test Cases
    // ═══════════════════════════════════════
    public function testAutoCreateByTransactionSuccess()
    {
        $this->post('/api/transactions/', $this->transactionPayload(), $this->authHeader())
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    'customer' => [
                        'nik'  => $this->customer->nik,
                        'name' => $this->customer->customer_name,
                    ],
                    'item' => [
                        'item_name' => $this->masterItem->item_name,
                    ],
                    'quantity'    => '3',
                    'description' => 'Test Description V2',
                    'amount'      => 19000,
                    'total'       => 19000 * 3,
                    'receivable'  => [
                        'total_amount'     => 19000 * 3,
                        'paid_amount'      => 19000 * 3,
                        'remaining_amount' => 0,
                        'status'           => 'PAID',
                        'receivable_items' => [
                            [
                                'item_id'  => $this->masterItem->id,
                                'qty'      => '3',
                                'price'    => 19000,
                                'subtotal' => 19000 * 3,
                            ]
                        ],
                        'receivable_payments' => [
                            [
                                'amount'         => 19000 * 3,
                                'payment_method' => 'CASH',
                            ]
                        ],
                    ]
                ]
            ]);
    }

    public function testAutoUpdateSuccess() {
        $this->testAutoCreateByTransactionSuccess();

        $this->transaction = Transaction::query()->first();
        $this->assertNotNull($this->transaction, 'Transaction tidak ditemukan setelah create');
        
        $this->patch(
            '/api/transactions/' . $this->transaction->id, 
            $this->transactionUpdatePayload(),
            $this->authHeader()
        )->assertStatus(200)
        ->assertJson([
            "data" => [
                "customer" => [
                    "nik" => $this->customer->nik,
                    "name" => $this->customer->customer_name,
                ],
                "item" => [
                    "item_name" => $this->masterItem->item_name,
                ],
                "quantity" => 5,
                "description" => "Test Update Partial Payment Description V2",
                "amount" => 19000,
                "total" => 19000 * 5,
                "receivable" => [
                    "total_amount" => 19000 * 5,
                    "paid_amount" => 3000,
                    "remaining_amount" => 92000,
                    "status" => "PARTIAL",
                    "receivable_items" => [
                        [
                            "item_id" => $this->masterItem->id,
                            "qty" => 5,
                            "price" => 19000,
                            "subtotal" => 19000 * 5,
                        ]
                    ],
                    "receivable_payments" => [
                        [
                            "amount" => 3000,
                            "payment_method" => "PARTIAL",
                        ]
                    ],
                ]
            ]
        ]);
    }

    // Contoh reuse di test lain — payload bisa di-override
    public function testCreateFailedUnauthorized()
    {
        $this->post('/api/transactions/', $this->transactionPayload())
            ->assertStatus(401);
    }

    public function testCreateFailedQuantityEmpty()
    {
        $this->post('/api/transactions/', 
            $this->transactionPayload(['quantity' => '']),  // override quantity saja
            $this->authHeader()
        )->assertStatus(400);
    }

    public function testCreateFailedCustomerNotFound()
    {
        $this->post('/api/transactions/', 
            $this->transactionPayload(['customer_id' => $this->customer->id + 99999]),
            $this->authHeader()
        )->assertStatus(404)
        ->assertJson([
            'error' => 'CUSTOMER_NOT_FOUND'
        ]);
    }

    public function testCreateFailedItemNotFound()
    {
        $this->post('/api/transactions/', 
            $this->transactionPayload(['item_id' => $this->masterItem->id + 99999]),
            $this->authHeader()
        )->assertStatus(404)
        ->assertJson([
            'error' => 'MASTER_ITEM_NOT_FOUND'
        ]);
    }

    public function testCreateFailedAmountInvalid()
    {
        $this->post('/api/transactions/', 
            $this->transactionPayload(['amount' => -5000]),
            $this->authHeader()
        )->assertStatus(400)
        ->assertJson([
            'errors' => [
                'amount' => [
                    'The amount field must be at least 0.'
                ]
            ]
        ]);
    }

    public function testCreateFailedQuantityZero()
    {
        $this->post('/api/transactions/', 
            $this->transactionPayload(['quantity' => 0]),
            $this->authHeader()
        )->assertStatus(400)
        ->assertJson([
            'errors' => [
                'quantity' => [
                    'The quantity field must be at least 1.'
                ]
            ]
        ]);
    }

    public function testCreateFailedMissingRequiredFields()
    {
        $this->post('/api/transactions/', 
            [], // payload kosong
            $this->authHeader()
        )->assertStatus(400)
        ->assertJson([
            'errors' => [
                'customer_id' => [
                    'The customer id field is required.'
                ],
                'item_id' => [
                    'The item id field is required.'
                ],
                'quantity' => [
                    'The quantity field is required.'
                ],
                'amount' => [
                    'The amount field is required.'
                ],
                'payment_method' => [
                    'The payment method field is required.'
                ],
                'paid_amount' => [
                    'The paid amount field is required.'
                ],
            ]
        ]);
    }

}