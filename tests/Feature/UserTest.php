<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testRegisterSuccess()
    {
        $this->post('/api/users', [
            'username' => 'khannedy',
            'password' => 'rahasia',
            'email' => 'Eko@pzn.com',
        ])->assertStatus(201)
        ->assertJson([
            "data" => [
            'username' => 'khannedy',
            'email' => 'Eko@pzn.com',
            ]
            ]);

            $user = User::where('username', 'khannedy')->first();
            self::assertNotNull($user->token);
            self::assertEquals($user->expiresIn, 10000);
    }

    public function testRegisterFailed()
    {
        $this->post('/api/users', [
            'username' => '',
            'password' => '',
        ])->assertStatus(400)
        ->assertJson([
            "errors" => [
                'password' => [
                    'The password field is required.'
                ],
            ]
            ]);
    }

    public function testRegisterUsernameAlreadyExists()
    {
        $this->testRegisterSuccess();
        $this->post('/api/users', [
            'username' => 'khannedy',
            'password' => 'rahasia',
            'fullname' => 'Eko Kurniawan Khannedy'
        ])->assertStatus(400)
        ->assertJson([
            "errors" => [
                    'USERNAME_EXISTS'
            ]
            ]);        
    }

    public function testRegisterEmailAlreadyExists()
    {
        $this->testRegisterSuccess();
        $this->post('/api/users', [
            'username' => 'Renan',
            'password' => 'rahasia',
            'fullname' => 'Junaedi',
            'email' => 'Eko@pzn.com',
        ])->assertStatus(400)
        ->assertJson([
            "errors" => [
                    'EMAIL_EXISTS'
            ]
            ]);        
    }

    public function testRegisterEmailFailed()
    {
        $this->post('/api/users', [
            'username' => 'Renan',
            'password' => 'rahasia',
            'fullname' => 'Muhammad Renan Ainur Rofiq',
            'email' => 'Ekopzn',
        ])->assertStatus(400)
        ->assertJson([
            "errors" => [
                'email' => [
                    'The email field must be a valid email address.'
                ]
            ]
            ]);
    }

    public function testLoginSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login', [
            'email' => 'test@pzn.com',
            'password' => 'test',
        ])->assertStatus(200)
        ->assertJson([
            "data" => [
                'email' => 'test@pzn.com',
                "username" => "test0",
            ]
            ]);

            $user = User::where('username', operator: 'test0')->first();
            self::assertNotNull($user->token);
            self::assertEquals($user->expiresIn, 10000);
    }

    public function testLoginFailed()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login', [
            'email' => 'renan@tes.com',
            'password' => 'test',
        ])->assertStatus(401)
        ->assertJson([
            "errors" => [
                        "EMAIL_PASSWORD_WRONG"
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current', [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->assertJson([
            'data' => [
                'username' => 'test0',
                'email' => 'test@pzn.com',
                'token' => 'test',
            ]
        ]);
    }

    public function testGetUnauthorize()
    {
        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current',
        [
            'Authorization' => 'salah'
        ])
        ->assertStatus(401)
        ->assertJson([
            'errors' => [
                'message' => [
                    'unauthorized'
                ]
            ]
        ]);
    }

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class]);
        $oldUser = User::where('username', 'test0')->first(); 

        $this->patch('/api/users/current', [
             'password' => 'baru'
        ],
        [
            'Authorization' => 'test'
        ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'username' => 'test0',
            ]
        ]);

        $newUser = User::where('username', 'test0')->first();  
        self::assertNotEquals($oldUser->password, $newUser->password);  
    }

    public function testUpdateUsernameExists()
    {
        $this->seed([UserSeeder::class]);
        $oldUser = User::where('username', 'test0')->first(); 

        $this->patch('/api/users/current', [
             'username' => 'test100'
        ],
        [
            'Authorization' => 'test'
        ])
        ->assertStatus(400)
        ->assertJson([
            "errors"=> [
                    "USERNAME_EXISTS"
                ]
        ]);
    }

    public function testUpdateEmailExists()
    {
        $this->seed([UserSeeder::class]);
        $oldUser = User::where('username', 'test')->first(); 

        $this->patch('/api/users/current', [
             'email' => 'test1@pzn.com'
        ],
        [
            'Authorization' => 'test'
        ])
        ->assertStatus(400)
        ->assertJson([
            "errors"=> [
                    "EMAIL_EXISTS"
                ]
        ]);
    }

    public function testUpdateAllSuccess(){
        $this->seed([UserSeeder::class]);
        $oldUser = User::where('username', 'test100')->first(); 

        $this->patch('/api/users/current', [
            'username' => 'test1',
            'email' => 'test1@hotmail.com',
            'passowrd' => 'hadir',
            'token' => 'test',
            'expiresIn' => NULL,
        ],
        [
            'Authorization' => 'test'
        ])
        ->assertStatus(200)
        ->assertJson([
            'data' => [
            'username' => 'test1',
            'email' => 'test1@hotmail.com',
            'token' => 'test',
            'expiresIn' => NULL,
            ]
        ]);

        $newUser = User::where('username', 'test1')->first();  
        self::assertNotEquals($oldUser->password, $newUser->password);  
    }

    public function testUpdateFailed(){
        $this->seed([UserSeeder::class]);   

        $this->patch('/api/users/current', [
             'fullname' => 'sadadsadsadsadsadadsadsadsadsadadsadsadsadsadadsadsadsadssadadsadsadsadsadadsadsadsadsadadsadsadsadsadadsadsadsadsadadsadsadsadsadadsadsadsadsadadsadsadsadsadadsadsadsadsadadsadsadsadsadadsadsadsadsadadsadsadsadsadadsadsadsadsadadsadsadsadsadadsadsadsadsadadsadsadsadsadadsadsadsadsadadsadsadsadsadadsadsadsadsadadsadsadsadsadadsadsadsadsadadsadsadsadsadadsadsadsadsadadsadsadsadsadadsadsadsadadadsadsadsadsadadsadsadsadsadadsadsadsadsadadsadsadsad'
        ],
        [
            'Authorization' => 'test'
        ])
        ->assertStatus(400)
        ->assertJson([
            'errors' => [
                'fullname' => [
                    'The fullname field must not be greater than 100 characters.'
                ]
            ]
        ]);
    }

    public function testLogoutSuccess(){
        $this->seed([UserSeeder::class]);
        $oldUser = User::where('username', 'test0')->first(); 

        $this->delete('/api/users/logout',[],[
                'Authorization' => 'test'
        ])
        ->assertStatus(200)
        ->assertJson([
            'data' => true
        ]);

        $newUser = User::where('username', 'test0')->first();
        self::assertNotEquals($oldUser->token, $newUser->token); 
       self::assertNull($newUser->token);
    }

    public function testLogoutFailed(){
        $this->seed([UserSeeder::class]);

        $this->delete('/api/users/logout',[],[
                'Authorization' => 'salah'
        ])
        ->assertStatus(401)
        ->assertJson([
            'errors' => [
                'message' => [
                    'unauthorized'
                ]
            ]
        ]);
    }
}
