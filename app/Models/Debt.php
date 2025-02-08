<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Debt extends Model
{
    protected $table = "debts";
    protected $primary_key = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $increamenting = true;

    protected $fillable = [
        'customer_id',
        'description',
        'amount_pay',
        'total',
    ];

    public function customers(): BelongsTo
    {
        return $this->belongsTo(Customer::class, "customer_id", "id");
    }
}
