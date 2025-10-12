<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    protected $fillable = [
        'owner_id',
        'item_id',
        'quantity',
        'cogs',
        'selling_price',
        'description',
        'created_by',
        'updated_by',
    ];

    public function asset_owner(): BelongsTo {
        return $this->belongsTo(AssetOwner::class, "owner_id", "id");
    }

    public function master_item(): BelongsTo
    {
        return $this->belongsTo(MasterItem::class, 'item_id', 'id');
    }

    public function getCogsPerUnitAttribute(): float
    {
        return $this->quantity ? $this->cogs * $this->quantity : 0;
    }

    public function getSellingPricePerUnitAttribute(): float
    {
        return $this->quantity ? $this->selling_price / $this->quantity : 0;
    }


}
