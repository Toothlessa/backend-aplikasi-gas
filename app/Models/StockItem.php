<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'created_by',
        'updated_by',
    ];

    public function transaction()
    {
        return $this->hasMany(Transaction::class, 'stock_id');
    }

    public function masteritem(): BelongsTo
    {
        return $this->belongsTo(MasterItem::class, "item_id", "id");
    }
}
