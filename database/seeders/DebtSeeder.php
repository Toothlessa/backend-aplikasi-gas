<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Debt;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DebtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customer = Customer::where('customer_name', 'test')->first();

        for($x=0; $x<5; $x++){
            Debt::create([
                'customer_id' => $customer->id,
                'description' => "test".$x,
                'amount_pay' => $x."000",
                'total'=> $x."0000"
            ]);
        }
    }
}
