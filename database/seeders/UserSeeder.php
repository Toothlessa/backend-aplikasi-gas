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
            'token' => 'test',
            'email' => 'test@pzn.com',
        ]);

        User::create([
            'username' => 'test100',
            'password' => Hash::make('test'),
            'token' => 'test1',
            'email' => 'test1@pzn.com',
        ]);
    }
}
