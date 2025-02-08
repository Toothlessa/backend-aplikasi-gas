<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    protected $fillable = [
        'name',
        'owner_id',
        'asset_name',
        'quantity',
        'cogs',
        'selling_price',
        'description'
    ];

    public function asset_owners(): BelongsTo {
        return $this->belongsTo(AssetOwner::class, "owner_id", "id");
    }
}
