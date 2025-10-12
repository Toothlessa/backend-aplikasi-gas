<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SearchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 20; $i++) {
            Customer::create([
                'customer_name' => 'test ' .$i,
                'nik'=> '3271' .$i,
                'email'=> 'test' .$i. '@gmail.com',
                'address'=> 'Jl. Ledeng Sindang Sari ' .$i,
                'phone' => '081'. $i,
                'active_flag' => 'Y',
            ]);
        }
    }
}
