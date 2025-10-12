<?php

namespace App\Repositories;

use App\Models\AssetOwner;
use Illuminate\Support\Facades\DB;

class AssetOwnerRepository
{   
    public function create($data)
    {
        return AssetOwner::create($data);
    }

    public function update(AssetOwner $assetOwner, $data)
    {
        $assetOwner->fill($data);
        $assetOwner->save();
        return $assetOwner;
    }

    public function findById($id)
    {
        return AssetOwner::find($id);
    }
    public function getAll()
    {
        return AssetOwner::all();
    }

    public function validateOwnerAssetNameExists($assetOwnerName)
    {
        return AssetOwner::where('name', $assetOwnerName)->exists();
    }
}