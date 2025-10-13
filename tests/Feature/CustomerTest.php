<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\SearchSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    //running unit test only from this file php artisan test --filter=CustomerTest
    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/customers', [
            'customer_name' => 'andi',
            'customer_type' => 'RT',
            'nik' => '3271040408420005',
            'email' => 'Eko@pzn.com',
            'address' => 'Jl.Ledeng Sindang Sari',
            'phone' => '087829190920'
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(201)
        ->assertJson([
            "data" => [
            'customer_name' => 'andi',
            'customer_type' => 'RT',
            'nik' => '3271040408420005',
            'email' => 'Eko@pzn.com',
            'address' => 'Jl.Ledeng Sindang Sari',
            'phone' => '087829190920'
            ]
            ]);
    }

    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/customers', [
            'customer_name' => '',
            'type' => 'RT',
            'nik' => 3,
            'email' => 'Eko@pzn.com',
            'phone' => '087829190920'
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(400)
        ->assertJson([
            "errors" => [
                'customer_name' => [
                    'The customer name field is required.'
                ],
            ]]);
    }

    public function testCreateCustomerAlreadyExists()
    {
        $this->testCreateSuccess();

        $this->post('/api/customers', [
            'customer_name' => 'khannedy',
            'customer_type' => 'RT',
            'nik' => '3271040408420005',
            'email' => 'Eko@pzn.com',
            'phone' => '087829190920'
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(400)
        ->assertJson([
            'error' =>  'CUSTOMER_EMAIL_EXISTS',
            ]);
    }

    // this test already unused
    // public function testCreateEmailAlreadyExists()
    // {
    //     $this->testCreateSuccess();

    //     $this->post('/api/customers', [
    //         'customer_name' => 'renan',
    //         'customer_type' => 'RT',
    //         'nik' => '3271040408410005',
    //         'email' => 'Eko@pzn.com',
    //         'phone' => '0919231'
    //     ],
    //     [
    //         'Authorization' => 'test'
    //     ])->assertStatus(400)
    //     ->assertJson([
    //         'error' =>  'CUSTOMER_EMAIL_EXISTS',
    //     ]);
    // }

    public function testCreateUnauthorized()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/customers', [
            'customer_name' => 'khannedy',
            'customer_type' => 'RT',
            'nik' => '3271040408420005',
            'email' => 'Eko@pzn.com',
            'phone' => '087829190920'
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

    public function testUpdateSuceccess()
    {
        $this->seed([UserSeeder::class, CustomerSeeder::class]);

        $customer = Customer::query()->limit(1)->first();

        $this->put('/api/customers/' .$customer->id, [
            'customer_name' => 'Ijat',
            'customer_type' => 'UM',
            'nik' => '119011',
            'email' => 'muhrenan@gmail.com',
            'address' => 'Jl.Ledeng Sindang Sari II',
            'phone' => '0811111',
        ], [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->assertJson([
            'data' => [
                'customer_name' => 'Ijat',
                'customer_type' => 'UM',
                'nik' => '119011',
                'email' => 'muhrenan@gmail.com',
                'address' => 'Jl.Ledeng Sindang Sari II',
                'phone' => '0811111',
            ]
            ]);
    }

    public function testUpdateValidationError()
    {
        $this->seed([UserSeeder::class, CustomerSeeder::class]);

        $customer = Customer::query()->limit(1)->first();

        $this->put('/api/customers/' .$customer->id, [
            'customer_name' => '',
            'customer_type' => 'UM',
            'nik' => '119011',
            'email' => 'muhrenan@gmail.com',
            'phone' => '0811111',
        ], [
            'Authorization' => 'test'
        ])->assertStatus(400)
        ->assertJson([
            'errors' => [
                    'customer_name' => [
                        'The customer name field is required.'
                    ]
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, CustomerSeeder::class]);
        $customer = Customer::where('customer_name', 'test')->first();

        $this->get('/api/customers/' . $customer->id,[
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'customer_name' => 'test',
                    'customer_type' => 'RT',
                    'nik' => '3271981923812912',
                    'email' => 'test@gmail.com',
                    'address' => 'Jl.Ledeng Sindang Sari',
                    'phone' => '087820977384',
                ]
            ]);
    }

    public function testGetNotFound()
    {
        $this->seed([UserSeeder::class, CustomerSeeder::class]);
        $user = User::where('username', 'test')->first();
        $customer = Customer::query()->limit(1)->first();

        $this->get('/api/customers/' . ($customer->id + 100),[
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                'error' => 'CUSTOMER_NOT_FOUND'
            ]);
    }

    public function testGetNotUnauthorized()
    {
        $this->seed([UserSeeder::class, CustomerSeeder::class]);
        $user = User::where('username', 'test')->first();
        $customer = Customer::query()->limit(1)->first();

        $this->get('/api/customers/' . $customer->id,[
            'Authorization' => 'rahasia'
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ]);
    }

    public function testCustomerNameAlreadyExists()
    {
        $this->seed([UserSeeder::class, CustomerSeeder::class]);

        $customer = Customer::query()->first();  

        $this->put('/api/customers/' .$customer->id, [
            'customer_name' => 'renan',
            'customer_type' => 'RT',
            'nik' => '119011',
            'email' => 'muhrenan@gmail.com',
            'phone' => '0811111',
        ], [
            'Authorization' => 'test'
        ])->assertStatus(400)
        ->assertJson([
            "error" => "CUSTOMER_NAME_EXISTS"
            ]);
    }

    public function testCustomerEmailAlreadyExists()
    {
        $this->seed([UserSeeder::class, CustomerSeeder::class]);

        $customer = Customer::query()->limit(1)->first();

        $this->put('/api/customers/' .$customer->id, [
            'customer_name' => 'Judi',
            'customer_type' => 'RT',
            'nik' => '119011',
            'email' => 'renan@gmail.com',
            'phone' => '0811111',
        ], [
            'Authorization' => 'test'
        ])->assertStatus(400)
        ->assertJson([
             "error" => "CUSTOMER_EMAIL_EXISTS"
            ]);
    }

    public function testSearchByCustomerName()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/customers?customer_name=test', [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }

    public function testSearchByCustomerEmail()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/customers?email=test', [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }

    public function testSearchByCustomerNik()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/customers?nik=327', [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }

    public function testSearchByCustomerAddress()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/customers?address=Sindang Sari', [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }
    public function testSearchByCustomerPhone()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/customers?phone=081', [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }

    public function testSearchNotFound()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/customers?nik=0812', [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(0, count($response['data']));
        self::assertEquals(0, $response['meta']['total']);
    }

    public function testSearchWithPage()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/customers?size=5&page=2', [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(5, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
        self::assertEquals(2, $response['meta']['current_page']);    
    }

    public function testGetAllSuccess()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/customers/all', [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->Json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testInactiveCustomer()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $customer = Customer::query()->limit(1)->first();
        $response = $this->patch('/api/customers/inactive/'.$customer->id, [],[
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->assertJson([
            'data' => [
                'active_flag' => 'N',
            ]
        ]);

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testImports_csv_file_successfully()
    {
        $this->seed([UserSeeder::class]);

        // Create a test CSV file
        $csvData = "customer_name,customer_type,nik,email,address,phone\nJohn Doe,RT,332121312,john@gmail.com,sinsar,082191\nJane Smith,UM,332121311,Jane@gmail.com,sinsara,082192";
        $file = UploadedFile::fake()->createWithContent('test.csv', $csvData);

        $response = $this->post('/api/customers/import-csv', ['csvFile' => $file],
        [
            'Authorization' => 'test'
        ])->assertStatus(200);

        $this->assertDatabaseHas('customers', ['customer_name' => 'John Doe', 'nik' => 332121312]);
        $this->assertDatabaseHas('customers', ['customer_name' => 'Jane Smith', 'nik' => 332121311]);
    }
}
