<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Transaction extends Model
{
    protected $table = "transactions";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'id',
        'trx_number',
        'item_id',
        'customer_id',
        'stock_id',
        'quantity',
        'description',
        'amount',
        'total',
        'created_by',
        'updated_by',
    ];

    public function masterItem(): BelongsTo
    {
        return $this->belongsTo(MasterItem::class, "item_id", "id");
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, "customer_id", "id");
    }

    public function stockItem()
    {
        return $this->belongsTo(StockItem::class, 'stock_id');
    }
}
