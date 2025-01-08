<?php

namespace Database\Seeders;

use App\Models\MasterItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterItemSearchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 20; $i++) {
            MasterItem::create([
                'item_name' => 'test ' .$i,
                'item_code'=> 'A001' .$i,
                'category'=> 'Makanan',
                'cost_of_goods_sold'=> $i. '0000',
                'selling_price'=> $i. '5000',
            ]);
        }
    }
}
