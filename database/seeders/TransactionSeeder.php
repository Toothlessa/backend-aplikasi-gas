<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\MasterItem;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username', 'test0')->first();
        $item = MasterItem::where('item_name', 'test')->first();
        $customer = Customer::where('customer_name', 'test')->first();

        for($x=0; $x<20; $x++){
        Transaction::create([
            'trx_number' => 'trxtest'. $x,
            'quantity' => $x,
            'amount' => 19000,
            'total' => 19000 * $x,
            'description' => 'Test Today',
            'item_id' => $item->id,
            'customer_id' => $customer->id,
            'created_by' => $user->id,
            'created_at' => Carbon::today(),
        ]);
    }

        for($i=0; $i<20; $i++){
            Transaction::create([
                'trx_number' => 'trxtestx'. $i,
                'quantity' => $i,
                'amount' => 19000,
                'total' => 19000 * $i,
                'description' => 'Test Tomorrow',
                'item_id' => $item->id,
                'customer_id' => $customer->id,
                'created_by' => $user->id,
                'created_at' => Carbon::tomorrow(),
            ]);
        }
    }
}
