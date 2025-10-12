<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\MasterItem;
use App\Models\StockItem;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->first();
        $item = MasterItem::query()->first();
        $customer = Customer::query()->first();

        for($x=0; $x<20; $x++){
        
            $stock = StockItem::create([
                'item_id' => $item->id,
                'stock' => $x,
                'cogs' => 16000,
                'selling_price' => 19000,
                'created_by' => $user->id,
            ]);
            
            Transaction::create([
                'stock_id' => $stock->id,
                'trx_number' => $x.'1',
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

            $stock = StockItem::create([
                'item_id' => $item->id,
                'stock' => $i,
                'cogs' => 16000,
                'selling_price' => 19000,
                'created_by' => $user->id,
            ]);

            Transaction::create([
                'quantity' => $i,
                 'trx_number' => $i.'2',
                'stock_id' => $stock->id,
                'amount' => 19000,
                'total' => 19000 * $i,
                'description' => 'Test Tomorrow',
                'item_id' => $item->id,
                'customer_id' => $customer->id,
                'created_by' => $user->id,
                'created_at' => Carbon::tomorrow(),
            ]);
        }

        for($i=0; $i<20; $i++){

            $stock = StockItem::create([
                'item_id' => $item->id,
                'stock' => $i,
                'cogs' => 16000,
                'selling_price' => 19000,
                'created_by' => $user->id,
            ]);
            
            Transaction::create([
                'quantity' => $i,
                'trx_number' => $i.'3',
                'stock_id' => $stock->id,
                'amount' => 19000,
                'total' => 19000 * $i,
                'description' => 'Test Yesterday',
                'item_id' => $item->id,
                'customer_id' => $customer->id,
                'created_by' => $user->id,
                'created_at' => Carbon::yesterday(),
            ]);
        }
    }
}
