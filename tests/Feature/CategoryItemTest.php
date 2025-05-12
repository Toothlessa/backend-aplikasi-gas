<?php

namespace Tests\Feature;

use App\Models\CategoryItem;
use Database\Seeders\CategoryItemSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class CategoryItemTest extends TestCase
{
    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/categoryitems', [
            'name' => 'Bahan Mentah',
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(201)
        ->assertJson([
            "data" => [
                'name' => 'Bahan Mentah',
                ]
            ]);
    }

    public function testCreateNameExists()
    {
        $this->testCreateSuccess();

        $this->post('/api/categoryitems', [
            'name' => 'Bahan Pokok',
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(400)
        ->assertJson([
                "errors" => "NAME_EXISTS"
            ]);
    }

    public function testGetCategorySuccess()
    {
        $this->testCreateSuccess();

        $categoryItem = CategoryItem::query()->first();
        $this->get('/api/categoryitems/'.$categoryItem->id,
        [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->assertJson([
                "data" => [
                    'name' => 'Bahan Pokok',
                ]
            ]);
    }

    public function testGetCategoryNotFound(){
        
        $this->testCreateSuccess();

        $categoryItem = CategoryItem::query()->first();
        $this->get('/api/categoryitems/'.$categoryItem->id+100,
        [
            'Authorization' => 'test'
        ])->assertStatus(404)
        ->assertJson([
                "errors" => "NOT_FOUND"
            ]);
    }

    public function testGetCategoryAll() {

        $this->seed([UserSeeder::class, CategoryItemSeeder::class]);

        $response = $this->get('/api/categoryitems/all',
        [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testGetCategoryActive() {

        $this->seed([UserSeeder::class, CategoryItemSeeder::class]);

        $response = $this->get('/api/categoryitems/active',
        [
            'Authorization' => 'test'
        ])->assertStatus(200)
        ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }

    public function testUpdateCategory() {

        $this->testCreateSuccess();

        $categoryItem = CategoryItem::query()->first();
        $this->patch('/api/categoryitems/'.$categoryItem->id, [
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

    public function testDeleteFailed() {

        $this->seed([UserSeeder::class, CategoryItemSeeder::class]);

        $categoryItem = CategoryItem::query()->first();
        $this->delete('/api/categoryitems/'.$categoryItem->id, [], 
        [
            'Authorization' => 'test'
        ])->assertStatus(400)
        ->assertJson([
            "errors" => "THIS_CATEGORY_EXISTS_IN_TRANSACTION"
            ]);
    }

    public function testInactiveCategory() {

        $this->seed([UserSeeder::class, CategoryItemSeeder::class]);

        $categoryItem = CategoryItem::query()->first();
        $this->patch('/api/categoryitems/inactive/'.$categoryItem->id, [], 
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
