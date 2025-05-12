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
        DB::delete("delete from transactions");
        DB::delete("delete from debts");
        DB::delete("delete from customers");
        DB::delete("delete from stock_items");
        DB::delete("delete from master_items");
        DB::delete("delete from category_items");
        DB::delete("delete from assets");
        DB::delete("delete from asset_owners");

        User::create([
            'username' => 'hanna',
            'password' => Hash::make('rahasia'),
            'token' => 'tes',
            'email' => 'hana@tes.com',
        ]);

        CategoryItem::create([
            'name' => 'Bahan Pokok',
        ]);

        $user = User::query()->first();

        $category = CategoryItem::query()->first();

        MasterItem::create([
            'item_name' => 'TESGAS LPG 3KG',
            'item_code' => 'TES01',
            'category_id' => $category->id,
            'cost_of_goods_sold' => 5000,
            'selling_price' => 10000,
            'created_by' => $user->id,
        ]);

        Customer::create([
            'customer_name' => 'Rizki Zulfianty',
            'nik' => '32710918929101',
            'email' => 'ica@gmail.com',
            'address' => 'Jl.Ledeng Sindang Sari',
            'phone' => '082919119191',
            'created_by' => $user->id,
        ]);
    }
}
