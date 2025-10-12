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
        'created_by',
        'updated_by',
    ];

    public function transaction(): HasMany
    {
        return $this->hasMany(Transaction::class, "item_id", "id");
    }

    public function stockitem(): HasMany
    {
        return $this->hasMany(StockItem::class, "item_id", "id");
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'item_id', 'id');
    }

    public function categoryItem(): BelongsTo
    {
        return $this->belongsTo(CategoryItem::class, 'category_id', 'id');
    }

}
