<?php

namespace Tests\Feature;

use App\Models\AssetOwner;
use Database\Seeders\AssetOwnerSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class AssetOwnerTest extends TestCase
{
    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/assetowners', [
            'name' => 'renan',
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(201)
        ->assertJson([
            "data" => [
                'name' => 'renan',
                ]
            ]);
    }

    public function testCreateNameExists()
    {
        $this->testCreateSuccess();

        $this->post('/api/assetowners', [
            'name' => 'renan',
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(400)
        ->assertJson([
                "errors" => "NAME_EXISTS"
            ]);
    }

    public function testGetOwnerSuccess()
    {
        $this->testCreateSuccess();

        $assetOwner = AssetOwner::query()->first();
        $this->get('/api/assetowners/'.$assetOwner->id,
        [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->assertJson([
                "data" => [
                    'name' => 'renan',
                ]
            ]);
    }

    public function testGetOwnerNotFound(){
        
        $this->testCreateSuccess();

        $assetOwner = AssetOwner::query()->first();
        $this->get('/api/assetowners/'.$assetOwner->id+1,
        [
            'Authorization' => 'test'
        ])->assertStatus(404)
        ->assertJson([
                "errors" => "NOT_FOUND"
            ]);
    }

    public function testGetOwnerAll() {

        $this->seed([UserSeeder::class, AssetOwnerSeeder::class]);

        $response = $this->get('/api/assetowners/all',
        [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testUpdateOwner() {

        $this->testCreateSuccess();

        $assetOwner = AssetOwner::query()->first();
        $this->patch('/api/assetowners/'.$assetOwner->id, [
            "name" => 'Barbie',
        ], [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->assertJson([
            'data' => [
                'name' => 'Barbie'
            ]
            ]);
    }

    public function testDeleteSuccess() {

        $this->seed([UserSeeder::class, AssetOwnerSeeder::class]);

        $assetOwner = AssetOwner::where('name', 'test0')->first();
        $this->delete('/api/assetowners/'.$assetOwner->id, [], 
        [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->assertJson([
            'data' => true
            ]);
    }

    public function testInactiveOwner() {

        $this->seed([UserSeeder::class, AssetOwnerSeeder::class]);

        $assetOwner = AssetOwner::query()->first();
        $this->patch('/api/assetowners/inactive/'.$assetOwner->id, [], 
        [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->assertJson([
            'data' => [
                'active_flag' => 'N'
            ]
            ]);
    }
}
