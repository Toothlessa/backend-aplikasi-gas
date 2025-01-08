<?php

namespace Tests\Feature;

use App\Models\MasterItem;
use Database\Seeders\MasterItemSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StockItemTest extends TestCase
{
    
    public function testInputStockSuccess()
    {
        $this->seed([UserSeeder::class, MasterItemSeeder::class]);
        $masterItem = MasterItem::query()->limit(1)->first();

        $this->post('/api/stockitems/' .($masterItem->id), [
            'stock' => 100,
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(status: 201)
        ->assertJson([
            'data' => [
                'stock' => 100,
            ]
        ]);
    }
}
