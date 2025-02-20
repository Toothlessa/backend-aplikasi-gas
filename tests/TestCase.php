<?php

namespace Tests;

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
        DB::delete("delete from assets");
        DB::delete("delete from asset_owners");
        // User::create([
        //     'username' => 'hanna',
        //     'password' => Hash::make('rahasia'),
        //     'token' => 'tes',
        //     'email' => 'tes@tes.com',
        // ]);
    }
}
