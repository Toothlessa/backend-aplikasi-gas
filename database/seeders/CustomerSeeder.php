<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username', 'test0')->first();
        Customer::create([
            'customer_name' => 'test',
            'nik' => '3271981923812912',
            'email' => 'test@gmail.com',
            'address' => 'Jl.Ledeng Sindang Sari',
            'phone' => '087820977384',
            'created_by' => $user->id,
        ]);

        Customer::create([
            'customer_name' => 'renan',
            'nik' => '32131239129',
            'email' => 'renan@gmail.com',
            'address' => 'Jl.Ledeng Sindang Sari',
            'phone' => '082718291',
            'created_by' => $user->id,
        ]);
    }
}
