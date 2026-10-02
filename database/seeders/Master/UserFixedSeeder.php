<?php

namespace Database\Seeders\Master;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserFixedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'renan',
            'email' => 'muhrenan@gmail.com',
            'password' => Hash::make('rahasia'),
            'phone' => '087770364644',
        ]);
    }
}
