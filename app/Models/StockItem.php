<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StockItem extends Model
{
    protected $table = "stock_items";
    protected $primary_key = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $increamenting = true;

    protected $fillable = [
        'item_id',
        'stock',
    ];

    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class, "stock_id", "id");
    }

    public function masteritem(): BelongsTo
    {
        return $this->belongsTo(MasterItem::class, "item_id", "id");
    }
}
