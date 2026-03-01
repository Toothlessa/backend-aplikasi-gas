<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Transaction extends Model
{
    # Call the boot function created by from Blameable trait
    use Blameable;
    protected $table = "transactions";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'trx_number',
        'item_id',
        'customer_id',
        'stock_id',
        'quantity',
        'description',
        'amount',
        'total',
    ];

    public function masterItem(): BelongsTo{
        return $this->belongsTo(MasterItem::class, "item_id", "id");
    }

    public function customer(): BelongsTo{
        return $this->belongsTo(Customer::class, "customer_id", "id");
    }

    public function stockItem(): BelongsTo{
        return $this->belongsTo(StockItem::class, 'stock_id');
    }

    public function receivables(): MorphOne {
        return $this->morphOne(Receivable::class, 'source');
    }

     #boot function
    protected static function booted()
    {
        static::saving(function ($model) {
            $model->total = $model->amount * $model->quantity;
        });
    }
}
