<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceivablePayment extends Model
{
    #traits call created by and updated by function
    use Blameable;
    protected $table = "receivable_payments";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'receivable_id',
        'payment_date',
        'amount',
        'payment_method',
        'description',
    ];

    #relationship
    public function receivable(): BelongsTo
    {
        return $this->belongsTo(Receivable::class, "receivable_id", "id");
    }
}