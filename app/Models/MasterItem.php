<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MasterItem extends Model
{
    protected $table = "master_items";
    protected $primary_key = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $increamenting = true;

    protected $fillable = [
        'item_name',
        'item_code',
        'item_type',
        'category_id',
        'cost_of_goods_sold',
        'selling_price',
    ];

    public function transaction(): HasMany
    {
        return $this->hasMany(Transaction::class, "item_id", "id");
    }

    public function stockitem(): HasMany
    {
        return $this->hasMany(StockItem::class, "item_id", "id");
    }
    // public function created_by(): BelongsTo
    // {
    //     return $this->belongsTo(User::class,'created_by', 'id');
    // }

    // public function updatedBy(): BelongsTo
    // {
    //     return $this->belongsTo(User::class,'updated_by', 'id');
    // }
}
