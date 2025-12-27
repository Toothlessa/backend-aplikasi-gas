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

    public function getDetailAsset($ownerId, $itemId)
    {
        return Asset::query()
            ->join('asset_owners AS ao', 'ao.id', '=', 'assets.owner_id')
            ->join('master_items AS mi', 'mi.id', '=', 'assets.item_id')
            ->select(
                'assets.*',
                'ao.name',
                'mi.item_name'
            )
            ->where('assets.owner_id', $ownerId)
            ->where('assets.item_id', $itemId)
            ->orderBy('assets.created_at')
            ->get();
    }

    public function getSummaryAssetByItemId($itemId) {
        return Asset::where('item_id', $itemId)->sum('quantity');
    }

}

