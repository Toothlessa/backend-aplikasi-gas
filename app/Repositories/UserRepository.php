<?php

namespace App\Repositories;

use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRepository
{
    public function create($data)
    {
        return User::create($data);
    }

    public function update(User $user, $data)
    {
        $user->fill($data);
        $user->save();

        return $user;
    }

    public function findById()
    {
         return Auth::user();
    }

    public function findUserByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public function validateUsernameExists(string $userName)
    {
        return User::where('username', $userName)->exists();
    }

    public function validateEmailExists($email)
    {
        return User::where('email', $email)->exists();
    }

    public function validatePhoneExists($phone) 
    {
        return User::where('phone', $phone)->exists();
    }

    public function hashCheckPassword($value, $hashedValue)
    {
        return Hash::check($value, $hashedValue);
    }

    public function hashingPassword($password)
    {
        return Hash::make($password);
    }

    public function generateTokenUuid()
    {
        return Str::uuid()->toString();
    }
}