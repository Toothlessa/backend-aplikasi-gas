<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoryItem extends Model
{
    protected $table = "category_items";
    protected $primary_key = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $increamenting = true;

    protected $fillable = [
        'name',
    ];

    public function transaction(): HasMany
    {
        return $this->hasMany(MasterItem::class, "category_id", "id");
    }
}
