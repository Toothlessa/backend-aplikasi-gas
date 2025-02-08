<?php

namespace Database\Seeders;

use App\Models\AssetOwner;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssetOwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $user = User::query()->first();
        for($i=0; $i<5; $i++) {
            AssetOwner::create([
                'name' => 'test'.$i,
                'created_by' => $user->id,
            ]);

        }
    }
}
