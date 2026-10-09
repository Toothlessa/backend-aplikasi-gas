<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\Master\UserFixedSeeder;
use Database\Seeders\Master\CustomerFixedSeeder;
use Database\Seeders\Master\CategoryItemFixedSeeder;
use Database\Seeders\Master\MasterItemFixedSeeder;
use Database\Seeders\Master\AssetOwnerFixedSeeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserFixedSeeder::class,
            CustomerFixedSeeder::class,
            CategoryItemFixedSeeder::class,
            AssetOwnerFixedSeeder::class,
            MasterItemFixedSeeder::class,
        ]);
    }
}
