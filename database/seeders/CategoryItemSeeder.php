<?php

namespace Database\Seeders;

use App\Models\CategoryItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoryItemSeeder extends Seeder
{

    public function run(): void
    {
        CategoryItem::create([
            'name' => 'Bahan Pokok',
        ]);
        
        for($x=0; $x<5; $x++){
            CategoryItem::create([
                'name' => 'Bahan Pokok'.$x,
            ]);
        }
    }
}
