<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceivableItem extends Model
{
    #call created by and updated by
    use Blameable;
    protected $table = "receivable_items";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'receivable_id',
        'item_id',
        'qty',
        'price',
        'subtotal',
    ];

    #relationship
    public function receivable(): BelongsTo
    {
        return $this->belongsTo(Receivable::class, "receivable_id", "id");
    }
    public function masterItem(): BelongsTo
    {
        return $this->belongsTo(MasterItem::class, "item_id", "id");
    }

    #boot function
    protected static function booted()
    {
        static::saving(function ($model) {
            $model->subtotal = $model->price * $model->qty;
        });
    }
}