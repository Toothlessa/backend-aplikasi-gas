<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'test0',
            'password' => Hash::make('test'),
            'fullname' => 'test',
            'token' => 'test',
            'email' => 'test@pzn.com',
            'phone' => '081112233',
            'street' => 'Jl.Test 01',
            'city' => 'test',
            'province' => 'test',
            'postal_code' => '111111',
            'country' => 'test'
        ]);

        User::create([
            'username' => 'test100',
            'password' => Hash::make('test'),
            'fullname' => 'test1',
            'token' => 'test1',
            'email' => 'test1@pzn.com',
            'phone' => '0811122331',
            'street' => 'Jl.Test 01',
            'city' => 'test1',
            'province' => 'test1',
            'postal_code' => '111111',
            'country' => 'test1'
        ]);
    }
}
