<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'category',
        'cost_of_goods_sold',
        'selling_price',
    ];
    public function created_by(): BelongsTo
    {
        return $this->belongsTo(User::class,'created_by', 'id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'updated_by', 'id');
    }
}
