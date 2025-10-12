<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoryItem extends Model
{
    protected $table = "category_items";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;
 
    protected $fillable = [
        'name',
        'active_flag',
        'prefix',
        'inactive_date',
        'created_by',
        'updated_by',
    ];

    public function masterItem(): HasMany
    {
        return $this->hasMany(MasterItem::class, 'category_id', 'id');
    }
}
