<?php

namespace App\Repositories;

use App\Models\Asset;
use Illuminate\Support\Facades\DB;

class AssetRepository
{   
    public function create($data)
    {
        return Asset::create($data);
    }

    public function update(Asset $asset, array $data): Asset
    {
        $asset->fill($data);
        $asset->save();
        return $asset;
    }

    public function findById($id)
    {
         return Asset::find($id);
    }

    public function sumQtyByAssetId($id)
    {
       return Asset::where('id', $id)
                        ->sum('quantity');
    }

    public function summaryAssetOwner() {

    return DB::table('assets')
            ->join('asset_owners', 'asset_owners.id', 'assets.owner_id')
            ->join('master_items', 'assets.item_id', 'master_items.id')
            ->selectRaw('assets.owner_id, asset_owners.name, assets.item_id, master_items.item_name, 
                        SUM(assets.quantity) AS quantity, SUM(assets.cogs) AS cogs, SUM(assets.selling_price) AS selling_price')
            ->groupByRaw('assets.owner_id, asset_owners.name, assets.item_id')
            ->get();
    
    }

    public function getDetailAsset($ownerId, $itemId) {

    return DB::table('assets AS as')
            ->join('asset_owners AS ao', 'ao.id', 'as.owner_id')
            ->join('master_items AS mi', 'mi.id', 'as.item_id')
            -> select('as.id', 'as.owner_id', 'as.item_id', 'ao.name', 'mi.item_name', 
                        'as.description', 'as.quantity', 'as.cogs', 'as.selling_price', 'as.created_at')
            ->where('as.owner_id', $ownerId)
            ->where('as.item_id', $itemId)
            ->orderBy('as.created_at')
            ->get();
    }

    public function getSummaryAssetByItemId($itemId) {
        return Asset::where('item_id', $itemId)->sum('quantity');
    }

}

