<?php

namespace Database\Seeders\Master;

use App\Models\AssetOwner;
use Illuminate\Database\Seeder;

class AssetOwnerFixedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AssetOwner::create([
            'name' => 'toothless',
            'active_flag' => 'Y',
        ]);
    }
}
