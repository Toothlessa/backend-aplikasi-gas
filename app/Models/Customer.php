<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Customer extends Model
{
    protected $table = "customers";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'customer_name',
        'customer_type',
        'nik',
        'email',
        'address',
        'phone',
        'active_flag',
        'inactive_date',
        'created_by',
        'updated_by',
    ];

    public function transaction(): HasMany
    {
        return $this->hasMany(Transaction::class, "customer_id", "id");
    }

    public function debt(): HasMany
    {
        return $this->hasMany(Debt::class, "customer_id", "id");
    }
}
