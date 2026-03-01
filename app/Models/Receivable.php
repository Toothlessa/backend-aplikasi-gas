<?php

namespace App\Models;

use App\Models\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Receivable extends Model
{
    # Call the boot function created by from Blameable trait
    use Blameable;
    
    # Table Specification
    protected $table = "receivables";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'customer_id',
        'invoice_number',
        'invoice_date',
        'status',
        'description',
        'invoice_date',
        'due_date',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'source_type',
        'source_id',
    ];

    #relationship
    public function customer(): BelongsTo{
        return $this->belongsTo(Customer::class, "customer_id", "id");
    }

    public function source(): MorphTo{
        return $this->morphTo();
    }

    public function receivablePayment() {
        return $this->hasMany(ReceivablePayment::class, "receivable_id", "id");
    }

    #boot function
    protected static function booted()
    {
        static::saving(function ($model) {
            $model->remaining_amount = $model->total_amount - $model->paid_amount;
        });
    }
}