<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Customer extends Model
{
    protected $table = "customers";
    protected $primary_key = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $increamenting = true;

    protected $fillable = [
        'customer_name',
        'type',
        'nik',
        'email',
        'address',
        'phone',
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
