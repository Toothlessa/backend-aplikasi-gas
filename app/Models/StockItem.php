<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockItem extends Model
{
    # Call the boot function created by from Blameable trait
    use Blameable;
    protected $table = "stock_items";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'item_id',
        'stock',
        'cogs',
        'selling_price',
        'prev_stock_id',
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
