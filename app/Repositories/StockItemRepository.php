<?php

namespace App\Repositories;


use App\Models\StockItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockItemRepository
{
    public function create($data)
    {
        return StockItem::create($data);
    }

    public function update(StockItem $stock, array $data): StockItem
    {
        $stock->fill($data);
        $stock->save();
        return $stock;
    }

    public function findByIdOrFail(int $id): StockItem
    {
        return StockItem::query()->findOrFail($id);
    }

    public function findByItemName()
    {

    }

    public function findLatestStock(int $itemId): ?StockItem
    {
        // return StockItem::where("item_id", $itemId)->orderByDesc("id")->first();
           return StockItem::query()
                            ->where('item_id', $itemId)
                            ->latest('id')
                            ->first(); // ← otomatis null jika tidak ada
    }

    public function getCurrentStock() {

        return DB::table("stock_items")
        ->join("master_items", "stock_items.item_id", 'master_items.id')
        ->join('category_items', 'category_id', 'category_items.id')
        ->selectRaw("stock_items.item_id, master_items.item_name, master_items.item_code, category_items.name AS category,
                 sum(stock) as total_stock, master_items.cost_of_goods_sold, master_items.selling_price")
        ->groupBy("stock_items.item_id", "master_items.item_name","master_items.item_code", "category_items.name",
                "master_items.cost_of_goods_sold", "master_items.selling_price")
        ->get();

    }

    public function getDetailStockByItem($itemId) {

        return DB::table("stock_items")
                ->join("master_items", "stock_items.item_id", "master_items.id")
                ->join('category_items', 'category_id', 'category_items.id')
                ->selectRaw("stock_items.id, item_id, item_name, item_code, category_items.name AS category, stock, stock_items.created_at")
                ->where("prev_stock_id", "=", 0)
                ->where("item_id", $itemId)
                ->orderByDesc("stock_items.created_at")
                ->limit(3)
                ->get();
    }

    public function getStockByItemId($itemId)
    {
        return StockItem::where('item_id', $itemId)->sum('stock');
    }

    public function getFilledGasStock() {
        return DB::table('stock_items as si')
            ->join('master_items as mi', 'mi.id', '=', 'si.item_id')
            ->where('mi.item_name', 'GAS LPG 3KG ISI')
            ->sum('si.stock');
    }

    public function getFilledLpg3KgStockUntilYesterday() {
        return DB::table('stock_items as si')
            ->join('master_items as mi', 'mi.id', '=' ,'si.item_id')
            ->where('mi.item_name', 'GAS LPG 3KG ISI')
            ->whereDate('si.created_at', '<', Carbon::today())
            ->sum('si.stock');
    }

    public function getStockNotToday($itemId)
    {
        return StockItem::where('item_id', $itemId)
                        ->whereDate('created_at', '!=', Carbon::today())
                        ->sum('stock');
    }
}
