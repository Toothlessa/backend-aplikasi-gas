<?php

namespace Tests;

use App\Models\CategoryItem;
use App\Models\Customer;
use App\Models\MasterItem;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete("delete from users");
        DB::delete("delete from receivable_payments");
        DB::delete("delete from receivable_items");
        DB::delete("delete from receivables");
        DB::delete("delete from transactions");
        DB::delete("delete from debts");
        DB::delete("delete from customers");
        DB::delete("delete from stock_items");
        DB::delete("delete from assets");
        DB::delete("delete from master_items");
        DB::delete("delete from category_items");
        DB::delete("delete from asset_owners");

        User::updateOrCreate([
            'username' => 'hanna',
            'password' => Hash::make('rahasia'),
            'token' => 'tes',
            'email' => 'hana@tes.com',
        ]);

        Customer::updateOrCreate([
            'customer_name' => 'Umum',
            'customer_type' => 'RT',
            'nik' => '000',
            'email' => 'umum@test.com',
            'address' => 'jl.test',
            'phone' =>'+62123456789',
            'active_flag' => 'Y',
        ]);

        CategoryItem::updateOrCreate([
            'name' => 'Bahan Pokok',
            'prefix' => 'BP',
            'active_flag' => 'Y',
        ]);

        MasterItem::updateOrCreate([
           'item_name' => 'Gas LPG 3KG',
           'item_type' => 'ITEM',
           'category_id' => CategoryItem::query()->where('name', 'Bahan Pokok')->first()->id,
           'cost_of_goods_sold' => 16000,
           'selling_price' => 19000,
           'active_flag' => 'Y',
        ]);
    }
}
