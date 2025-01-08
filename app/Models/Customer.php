<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    protected $table = "customers";
    protected $primary_key = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $increamenting = true;

    protected $fillable = [
        'customer_name',
        'nik',
        'email',
        'address',
        'phone',
    ];

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'updated_by', 'id');
    }

    public function created_by(): BelongsTo
    {
        return $this->belongsTo(User::class,'created_by', 'id');
    }
}
