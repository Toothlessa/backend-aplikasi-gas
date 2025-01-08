<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete("delete from users");
        DB::delete("delete from customers");
        DB::delete("delete from stock_items");
        DB::delete("delete from master_items");
    }
}
