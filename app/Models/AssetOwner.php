<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetOwner extends Model
{
    protected $table = "asset_owners";
    protected $primary_key = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $increamenting = true;

    protected $fillable = [
        // 'id',
        'name',
    ];

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, "owner_id", "id");
    }

}
