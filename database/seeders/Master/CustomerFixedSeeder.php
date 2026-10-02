<?php

namespace Database\Seeders\Master;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerFixedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::create([
            'customer_name' => 'Umum',
            'customer_type' => 'RT',
            'nik' => 0,
            'email' => 'umum@dummy.com',
            'address' => '0',
            'phone' => '0',
        ]);
    }
}
