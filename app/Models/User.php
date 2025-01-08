<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Model implements Authenticatable
{
    protected $table = "users";
    protected $primary_key = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $increamenting = true;

    protected $fillable = [
        'username',
        'password',
        'fullname',
        'email',
        'phone',
        'street',
        'city',
        'province',
        'postal_code',
        'country',
    ];

    public function createdBy(): HasMany
    {
        return $this->hasMany(Customer::class,'created_by','id');
    }

    public function updatedBy(): HasMany
    {
        return $this->hasMany(Customer::class,'update_by','id');
    }

    public function getAuthIdentifierName()
    {
        return 'username';
    }

    public function getAuthIdentifier()
    {
        return $this->username;
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getAuthPasswordName()
    {
        return 'password';
    }

    public function getRememberToken()
    {
        return $this->token;
    }

    public function setRememberToken($value)
    {
        $this->token = $value;
    }

    public function getRememberTokenName()
    {
        return 'token';
    }
}
