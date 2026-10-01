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
                'customer' => [
                    'nik'   => $customer->nik,
                    'name'  => $customer->customer_name,
                ],
                'item' => [
                    'item_name' => $masterItem->item_name,
                ],
                'quantity'      => '3',
                'description'   => 'Test Description',
                'amount'        => 19000,
                'total'         => 19000 * 3,
                'receivable'    => [
                    'total_amount'     => 19000 * 3,
                    'paid_amount'      => 19000 * 3,
                    'remaining_amount' => 0,
                    'status'           => 'PAID',
                    'receivable_items' => [
                        [
                            'item_id'  => $masterItem->id,
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
                'customer' => [
                    'nik'   => $customer->nik,
                    'name'  => $customer->customer_name,
                ],
                'item' => [
                    'item_name' => $masterItem->item_name,
                ],
                'quantity'      => '2',
                'description'   => 'Test Partial',
                'amount'        => 19000,
                'total'         => 19000 * 2,
                'receivable'    => [
                    'total_amount'     => 19000 * 2,
                    'paid_amount'      => 5000,
                    'remaining_amount' => (19000 * 2) - 5000,
                    'status'           => 'PARTIAL',
                    'receivable_items' => [
                        [
                            'item_id'  => $masterItem->id,
                            'qty'      => '2',
                            'price'    => 19000,
                            'subtotal' => 19000 * 2,
                        ]
                    ],
                    'receivable_payments' => [
                        [
                            'amount'         => 5000,
                            'payment_method' => 'PARTIAL',
                        ]
                    ],
                ]
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
                'customer' => [
                    'nik'   => $customer->nik,
                    'name'  => $customer->customer_name,
                ],
                'item' => [
                    'item_name' => $masterItem->item_name,
                ],
                'quantity'      => '1',
                'description'   => 'Create New Transaction',
                'amount'        => 19000,
                'total'         => 19000 * 1,
                'receivable'    => [
                    'total_amount'     => 19000 * 1,
                    'paid_amount'      => 19000 * 1,
                    'remaining_amount' => 0,
                    'status'           => 'PAID',
                    'receivable_items' => [
                        [
                            'item_id'  => $masterItem->id,
                            'qty'      => '1',
                            'price'    => 19000,
                            'subtotal' => 19000 * 1,
                        ]
                    ],
                    'receivable_payments' => [
                        [
                            'amount'         => 19000 * 1,
                            'payment_method' => 'CASH',
                        ]
                    ],
                ]
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

    public function testCreateUnauthorized(){
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
            'description'   => 'Test Unauthorized',
            'amount'        => 19000,
            'payment_method'=> 'CASH',
            'paid_amount'   => 19000,
        ];

        $this->postJson(
            '/api/transactions',
            $payload,
            ['Authorization' => 'wrong-token']
        )
        ->assertStatus(401)
        ->assertJson([
            'errors' => [
                'message' => [
                    'unauthorized'
                ]
            ]
        ]);
    }

    public function testCreateMissingRequiredFields(){
        $this->seed([
                    UserSeeder::class,
                    CategoryItemSeeder::class,
                    MasterItemSeeder::class,
                    StockItemSeeder::class,
                    CustomerSeeder::class
                ]);

        $payload = [];

        $this->postJson(
            '/api/transactions',
            $payload,
            ['Authorization' => 'test']
        )
        ->assertStatus(400)
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

    public function testCreateInvalidPaymentMethod(){
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
            'description'   => 'Test Invalid Payment Method',
            'amount'        => 19000,
            'payment_method'=> 'TRANSFER',
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
                'payment_method' => [
                    'The selected payment method is invalid.'
                ]
            ]
        ]);
    }

    public function testCreateDescriptionExceedsMaxLength(){
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
            'quantity'      => '1',
            'description'   => str_repeat('A', 101),
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
                'description' => [
                    'The description field must not be greater than 100 characters.'
                ]
            ]
        ]);
    }

    public function testCreateQuantityZero(){
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
            'quantity'      => '0',
            'description'   => 'Test Zero Quantity',
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

    public function testCreatePaidAmountZero(){
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
            'description'   => 'Test Zero Paid Amount',
            'amount'        => 19000,
            'payment_method'=> 'PARTIAL',
            'paid_amount'   => 0,
        ];

        $this->postJson(
            '/api/transactions',
            $payload,
            ['Authorization' => 'test']
        )
        ->assertStatus(201)
        ->assertJson([
            "data" => [
                'customer' => [
                    'nik'   => $customer->nik,
                    'name'  => $customer->customer_name,
                ],
                'item' => [
                    'item_name' => $masterItem->item_name,
                ],
                'quantity'      => '2',
                'description'   => 'Test Zero Paid Amount',
                'amount'        => 19000,
                'total'         => 19000 * 2,
                'receivable'    => [
                    'total_amount'     => 19000 * 2,
                    'paid_amount'      => 0,
                    'remaining_amount' => 19000 * 2,
                    'status'           => 'PARTIAL',
                    'receivable_items' => [
                        [
                            'item_id'  => $masterItem->id,
                            'qty'      => '2',
                            'price'    => 19000,
                            'subtotal' => 19000 * 2,
                        ]
                    ],
                    'receivable_payments' => [
                        [
                            'amount'         => 0,
                            'payment_method' => 'PARTIAL',
                        ]
                    ],
                ]
            ]
        ]);
    }

    // =========================================================================
    // UPDATE TRANSACTION TESTS
    // =========================================================================

    /**
     * Helper: seed baseline data + create one transaction via API,
     * kemudian return array ['transaction', 'customer', 'masterItem'].
     *
     * Dipakai oleh semua test update agar tidak duplikasi seed logic.
     */
    private function seedAndCreateTransaction(): array
    {
        $this->seed([
            UserSeeder::class,
            CategoryItemSeeder::class,
            MasterItemSeeder::class,
            StockItemSeeder::class,
            CustomerSeeder::class,
            TransactionSeeder::class,
        ]);

        $transaction = Transaction::first();
        $customer    = Customer::query()->first();
        $masterItem  = MasterItem::query()->first();

        return compact('transaction', 'customer', 'masterItem');
    }

    /** Update berhasil – pembayaran CASH (lunas penuh) */
    // public function testUpdateSuccess()
    // {
    //     // [
    //     //     'transaction' => $transaction,
    //     //     'customer'    => $customer,
    //     //     'masterItem'  => $masterItem
    //     // ] = $this->seedAndCreateTransaction();
    //     $this->seed([
    //                 UserSeeder::class,
    //                 CategoryItemSeeder::class,
    //                 MasterItemSeeder::class,
    //                 StockItemSeeder::class,
    //                 CustomerSeeder::class,
    //                 TransactionSeeder::class,
    //             ]);

    //     $masterItem   = MasterItem::query()->first();
    //     $customer     = Customer::query()->first();
    //     $transaction  = Transaction::query()->first();

    //     $payload = [
    //         'item_id'        => $masterItem->id,
    //         'customer_id'    => $customer->id,
    //         'quantity'       => '3',
    //         'description'    => 'Test Update Description',
    //         'amount'         => 19000,
    //         'payment_method' => 'CASH',
    //         'paid_amount'    => 19000,
    //     ];

    //     $this->patchJson(
    //         "/api/transactions/{$transaction->id}",
    //         $payload,
    //         ['Authorization' => 'test']
    //     )
    //     ->assertStatus(200)
    //     ->assertJson([
    //         'data' => [
    //             'customer' => [
    //                 'nik'  => $customer->nik,
    //                 'name' => $customer->customer_name,
    //             ],
    //             'item' => [
    //                 'item_name' => $masterItem->item_name,
    //             ],
    //             'quantity'    => '3',
    //             'description' => 'Test Update Description',
    //             'amount'      => 19000,
    //             'total'       => 19000 * 3,
    //             'receivable'  => [
    //                 'total_amount'     => 19000 * 3,
    //                 'paid_amount'      => 19000 * 3,
    //                 'remaining_amount' => 0,
    //                 'status'           => 'PAID',
    //                 'receivable_items' => [
    //                     [
    //                         'item_id'  => $masterItem->id,
    //                         'qty'      => '3',
    //                         'price'    => 19000,
    //                         'subtotal' => 19000 * 3,
    //                     ]
    //                 ],
    //                 'receivable_payments' => [
    //                     [
    //                         'amount'         => 19000 * 3,
    //                         'payment_method' => 'CASH',
    //                     ]
    //                 ],
    //             ],
    //         ]
    //     ]);
    // }

    // /** Update berhasil – pembayaran PARTIAL */
    // public function testUpdatePartialPayment()
    // {
    //     ['transaction' => $transaction, 'customer' => $customer, 'masterItem' => $masterItem]
    //         = $this->seedAndCreateTransaction();

    //     $payload = [
    //         'item_id'        => $masterItem->id,
    //         'customer_id'    => $customer->id,
    //         'quantity'       => '2',
    //         'description'    => 'Test Update Partial',
    //         'amount'         => 19000,
    //         'payment_method' => 'PARTIAL',
    //         'paid_amount'    => 5000,
    //     ];

    //     $this->patchJson(
    //         "/api/transactions/{$transaction->id}",
    //         $payload,
    //         ['Authorization' => 'test']
    //     )
    //     ->assertStatus(200)
    //     ->assertJson([
    //         'data' => [
    //             'customer' => [
    //                 'nik'  => $customer->nik,
    //                 'name' => $customer->customer_name,
    //             ],
    //             'item' => [
    //                 'item_name' => $masterItem->item_name,
    //             ],
    //             'quantity'    => '2',
    //             'description' => 'Test Update Partial',
    //             'amount'      => 19000,
    //             'total'       => 19000 * 2,
    //             'receivable'  => [
    //                 'total_amount'     => 19000 * 2,
    //                 'paid_amount'      => 5000,
    //                 'remaining_amount' => (19000 * 2) - 5000,
    //                 'status'           => 'PARTIAL',
    //                 'receivable_items' => [
    //                     [
    //                         'item_id'  => $masterItem->id,
    //                         'qty'      => '2',
    //                         'price'    => 19000,
    //                         'subtotal' => 19000 * 2,
    //                     ]
    //                 ],
    //                 'receivable_payments' => [
    //                     [
    //                         'amount'         => 5000,
    //                         'payment_method' => 'PARTIAL',
    //                     ]
    //                 ],
    //             ],
    //         ]
    //     ]);
    // }

    // /** Update berhasil – paid_amount = 0 (PARTIAL dengan belum bayar) */
    // public function testUpdatePaidAmountZero()
    // {
    //     ['transaction' => $transaction, 'customer' => $customer, 'masterItem' => $masterItem]
    //         = $this->seedAndCreateTransaction();

    //     $payload = [
    //         'item_id'        => $masterItem->id,
    //         'customer_id'    => $customer->id,
    //         'quantity'       => '2',
    //         'description'    => 'Test Update Zero Paid',
    //         'amount'         => 19000,
    //         'payment_method' => 'PARTIAL',
    //         'paid_amount'    => 0,
    //     ];

    //     $this->patchJson(
    //         "/api/transactions/{$transaction->id}",
    //         $payload,
    //         ['Authorization' => 'test']
    //     )
    //     ->assertStatus(200)
    //     ->assertJson([
    //         'data' => [
    //             'quantity'    => '2',
    //             'description' => 'Test Update Zero Paid',
    //             'amount'      => 19000,
    //             'total'       => 19000 * 2,
    //             'receivable'  => [
    //                 'total_amount'     => 19000 * 2,
    //                 'paid_amount'      => 0,
    //                 'remaining_amount' => 19000 * 2,
    //                 'status'           => 'PARTIAL',
    //                 'receivable_payments' => [
    //                     [
    //                         'amount'         => 0,
    //                         'payment_method' => 'PARTIAL',
    //                     ]
    //                 ],
    //             ],
    //         ]
    //     ]);
    // }

    /** Update – transaction ID tidak ditemukan → 404 */
    public function testUpdateNotFound()
    {
        $this->seed([
            UserSeeder::class,
            CategoryItemSeeder::class,
            MasterItemSeeder::class,
            StockItemSeeder::class,
            CustomerSeeder::class,
        ]);

        $customer   = Customer::query()->first();
        $masterItem = MasterItem::query()->first();

        $payload = [
            'item_id'        => $masterItem->id,
            'customer_id'    => $customer->id,
            'quantity'       => '1',
            'description'    => 'Not Found Test',
            'amount'         => 19000,
            'payment_method' => 'CASH',
            'paid_amount'    => 19000,
        ];

        $this->patchJson(
            '/api/transactions/999999',
            $payload,
            ['Authorization' => 'test']
        )
        ->assertStatus(404);
    }

    /** Update – token salah → 401 */
    public function testUpdateUnauthorized()
    {
        ['transaction' => $transaction, 'customer' => $customer, 'masterItem' => $masterItem]
            = $this->seedAndCreateTransaction();

        $payload = [
            'item_id'        => $masterItem->id,
            'customer_id'    => $customer->id,
            'quantity'       => '1',
            'description'    => 'Unauthorized Test',
            'amount'         => 19000,
            'payment_method' => 'CASH',
            'paid_amount'    => 19000,
        ];

        $this->patchJson(
            "/api/transactions/{$transaction->id}",
            $payload,
            ['Authorization' => 'wrong-token']
        )
        ->assertStatus(401)
        ->assertJson([
            'errors' => [
                'message' => [
                    'unauthorized'
                ]
            ]
        ]);
    }

    /** Update – quantity negatif → 400 */
    public function testUpdateQuantityMinus()
    {
        ['transaction' => $transaction, 'customer' => $customer, 'masterItem' => $masterItem]
            = $this->seedAndCreateTransaction();

        $payload = [
            'item_id'        => $masterItem->id,
            'customer_id'    => $customer->id,
            'quantity'       => '-1',
            'description'    => 'Minus Quantity',
            'amount'         => 19000,
            'payment_method' => 'CASH',
            'paid_amount'    => 19000,
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

    /** Update – amount negatif → 400 */
    public function testUpdateAmountMinus()
    {
        ['transaction' => $transaction, 'customer' => $customer, 'masterItem' => $masterItem]
            = $this->seedAndCreateTransaction();

        $payload = [
            'item_id'        => $masterItem->id,
            'customer_id'    => $customer->id,
            'quantity'       => '1',
            'description'    => 'Minus Amount',
            'amount'         => -19000,
            'payment_method' => 'CASH',
            'paid_amount'    => 19000,
        ];

        $this->patchJson(
            "/api/transactions/{$transaction->id}",
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

    /** Update – payload kosong → 400 semua field required */
    public function testUpdateMissingRequiredFields()
    {
        ['transaction' => $transaction] = $this->seedAndCreateTransaction();

        $this->patchJson(
            "/api/transactions/{$transaction->id}",
            [],
            ['Authorization' => 'test']
        )
        ->assertStatus(400)
        ->assertJson([
            'errors' => [
                'customer_id'    => ['The customer id field is required.'],
                'item_id'        => ['The item id field is required.'],
                'quantity'       => ['The quantity field is required.'],
                'amount'         => ['The amount field is required.'],
                'payment_method' => ['The payment method field is required.'],
                'paid_amount'    => ['The paid amount field is required.'],
            ]
        ]);
    }

    /** Update – payment_method tidak valid → 400 */
    public function testUpdateInvalidPaymentMethod()
    {
        ['transaction' => $transaction, 'customer' => $customer, 'masterItem' => $masterItem]
            = $this->seedAndCreateTransaction();

        $payload = [
            'item_id'        => $masterItem->id,
            'customer_id'    => $customer->id,
            'quantity'       => '1',
            'description'    => 'Invalid Method',
            'amount'         => 19000,
            'payment_method' => 'TRANSFER',
            'paid_amount'    => 19000,
        ];

        $this->patchJson(
            "/api/transactions/{$transaction->id}",
            $payload,
            ['Authorization' => 'test']
        )
        ->assertStatus(400)
        ->assertJson([
            'errors' => [
                'payment_method' => [
                    'The selected payment method is invalid.'
                ]
            ]
        ]);
    }

    /** Update – description melebihi 100 karakter → 400 */
    public function testUpdateDescriptionExceedsMaxLength()
    {
        ['transaction' => $transaction, 'customer' => $customer, 'masterItem' => $masterItem]
            = $this->seedAndCreateTransaction();

        $payload = [
            'item_id'        => $masterItem->id,
            'customer_id'    => $customer->id,
            'quantity'       => '1',
            'description'    => str_repeat('A', 101),
            'amount'         => 19000,
            'payment_method' => 'CASH',
            'paid_amount'    => 19000,
        ];

        $this->patchJson(
            "/api/transactions/{$transaction->id}",
            $payload,
            ['Authorization' => 'test']
        )
        ->assertStatus(400)
        ->assertJson([
            'errors' => [
                'description' => [
                    'The description field must not be greater than 100 characters.'
                ]
            ]
        ]);
    }

    /** Update – quantity = 0 → 400 */
    public function testUpdateQuantityZero()
    {
        ['transaction' => $transaction, 'customer' => $customer, 'masterItem' => $masterItem]
            = $this->seedAndCreateTransaction();

        $payload = [
            'item_id'        => $masterItem->id,
            'customer_id'    => $customer->id,
            'quantity'       => '0',
            'description'    => 'Zero Quantity',
            'amount'         => 19000,
            'payment_method' => 'CASH',
            'paid_amount'    => 19000,
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

    /**
     * Unit Test for Controller mocking the Service Dependency
     * Merefer ke pattern testCreate dengan melakukan mock pada TransactionService.
     */
    // public function testUpdateTransactionControllerWithMock()
    // {
    //     // Setup initial data to get valid IDs and models
    //     ['transaction' => $transaction, 'customer' => $customer, 'masterItem' => $masterItem]
    //         = $this->seedAndCreateTransaction();

    //     $payload = [
    //         'item_id'        => $masterItem->id,
    //         'customer_id'    => $customer->id,
    //         'quantity'       => '5',
    //         'description'    => 'Update With Mock Service',
    //         'amount'         => 19000,
    //         'payment_method' => 'CASH',
    //         'paid_amount'    => 95000,
    //     ];

    //     // Kita ubah state transaksi secara in-memory untuk mensimulasikan hasil dari service
    //     $transaction->quantity = '5';
    //     $transaction->description = 'Update With Mock Service';
    //     $transaction->total = 95000;

    //     // Mock TransactionService
    //     $mockService = \Mockery::mock(\App\Services\TransactionService::class);
    //     $mockService->shouldReceive('updateTransaction')
    //         ->once() // memastikan service dipanggil tepat satu kali
    //         ->with($transaction->id, \Mockery::on(function ($arg) use ($payload) {
    //             return $arg['quantity'] === $payload['quantity'] &&
    //                    $arg['description'] === $payload['description'];
    //         }))
    //         ->andReturn($transaction);

    //     // Bind mock ke service container Laravel
    //     $this->app->instance(\App\Services\TransactionService::class, $mockService);

    //     // Eksekusi API
    //     $this->patchJson(
    //         "/api/transactions/{$transaction->id}",
    //         $payload,
    //         ['Authorization' => 'test']
    //     )
    //     ->assertStatus(200)
    //     ->assertJson([
    //         'data' => [
    //             'quantity'    => '5',
    //             'description' => 'Update With Mock Service',
    //             'total'       => 95000,
    //         ]
    //     ]);
    // }

}
