<?php

namespace Database\Seeders\Master;

use App\Models\CategoryItem;
use Illuminate\Database\Seeder;

class CategoryItemFixedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CategoryItem::create([
            'name' => 'Bahan Pokok',
            'prefix' => 'BP',
        ]);
    }
}
