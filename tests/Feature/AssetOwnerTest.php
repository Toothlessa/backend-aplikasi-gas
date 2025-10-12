<?php

namespace Tests\Feature;

use App\Models\AssetOwner;
use Database\Seeders\AssetOwnerSeeder;
use Database\Seeders\UserSeeder;
use Carbon\Carbon;
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
                'name'        => 'renan',
                'active_flag' => 'Y'
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
                "errors" => "OWNER_NAME_EXISTS"
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
                     'active_flag' => 'Y'
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
                "errors" => "OWNER_NOT_FOUND"
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
                'name' => 'Barbie',
                 'active_flag' => 'Y'
            ]
            ]);
    }

    public function testInactiveOwner() 
    {
        $this->testCreateSuccess();

        $assetOwner = AssetOwner::query()->first();
        $response = $this->patch('/api/assetowners/inactive/'.$assetOwner->id, [], 
        [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->assertJson([
            'data' => [
                'active_flag' => 'N',
            ]
            ]);
        // ambil hasil inactive_date
        $inactiveDate = Carbon::parse($response->json('data.inactive_date'))->format('d-m-y');
        // bikin expected value
        $expected = Carbon::now()->format('d-m-y');
        // bandingkan hasil
        $this->assertEquals($expected, $inactiveDate);
    }

     public function testActivateOwner() {
        $this->testInactiveOwner();

        $assetOwner = AssetOwner::query()->first();

        $this->patch('/api/assetowners/inactive/'.$assetOwner->id, [], 
        [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->assertJson([
            'data' => [
                'active_flag' => 'Y',
                'inactive_date' => null,
            ]
            ]);
    }
}
