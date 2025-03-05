<?php

namespace Database\Seeders;

use App\Models\CategoryItem;
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
        $category = CategoryItem::query()->first();

        for ($i = 0; $i < 9; $i++) {
            MasterItem::create([
                'item_name' => 'test ' .$i,
                'item_code'=> 'BP0' .$i,
                'category_id'=> $category->id,
                'cost_of_goods_sold'=> $i. '0000',
                'selling_price'=> $i. '5000',
            ]);
        }
    }
}
