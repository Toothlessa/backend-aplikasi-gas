<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        Customer::create([
            'customer_name' => 'test',
            'customer_type' => 'RT',
            'nik' => '3271981923812912',
            'email' => 'test@gmail.com',
            'address' => 'Jl.Ledeng Sindang Sari',
            'phone' => '087820977384',
        ]);

        Customer::create([
            'customer_name' => 'renan',
            'customer_type' => 'RT',
            'nik' => '32131239129',
            'email' => 'renan@gmail.com',
            'address' => 'Jl.Ledeng Sindang Sari',
            'phone' => '082718291',
        ]);
    }
}
