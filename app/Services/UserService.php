<?php

namespace App\Services;

use Illuminate\Http\Exceptions\HttpResponseException;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

class UserService
{
    protected $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function register($data)
    {
        $this->validateUsernameExists($data['username']);
        $this->validateEmailExists($data['email']);

        # Hash the password
        $password = $this->hashingPassword($data['password']);
        # Generate token
        $token = $this->generateUuid();

        $user = [
            'username'  => $data['username'],
            'email'     => $data['email'],
            'password'  => $password,
            'token'     => $token,
            'expiresIn' => 100000,
        ];

        if (!empty($data['phone'])) {
            $user['phone'] = $data['phone'];
        }

        $user = $this->repository->create($user);

        return $user;
    }

    public function update($data)
    {
        $user = Auth::user();

        if($data['username'] != $user->username) {
            $this->validateUsernameExists($data['username']);
            $user->username = $data['username'];
        }

        if($data['email'] != $user->email) {
            $this->validateEmailExists($data['email']);
            $user->email = $data['email'];
        }

        $user = $this->repository->update($user, $data);

        return $user;
    }

    public function findById()
    {
        $user = $this->repository->findById();

        if(!$user) {
            throw new HttpResponseException(response()->json([
                'error' => 'ERROR_LOGIN',
            ])->setStatusCode(404));
        }

        return $user;
    }

    public function login($data)
    {
        $user = $this->repository->findUserByEmail($data['email']);
        
        if(!$user || !$this->repository->hashCheckPassword($data['password'], $user->password)) {
            throw new HttpResponseException(response()->json([
                'error' => 'EMAIL_PASSWORD_WRONG',
            ])->setStatusCode(401));
        }

        $user->token = $this->generateUuid();
        $user->expiresIn = 10000;
        $user->save();

        return $user;
    }

    public function logout()
    {
        $user = $this->findById();

        $user->token = null;
        $user->save();

        return true;
    }

    public function validateUsernameExists($userName)
    {
        $userName = $this->repository->validateUsernameExists($userName);

        if($userName) {
            throw new HttpResponseException(response()->json([
                'error' => 'USERNAME_EXISTS',
            ])->setStatusCode(400));
        }

        return true;
    }

    public function validateEmailExists($email)
    {
        $email = $this->repository->validateEmailExists($email);

        if($email) {
            throw new HttpResponseException(response()->json([
                'error' => 'EMAIL_EXISTS',
            ])->setStatusCode(400));
        }

        return true;
    }

    public function validatePhoneExists($phone) {
        $phone = $this->repository->validatePhoneExists($phone);

        if($phone) {
            throw new HttpResponseException(response()->json([
                'error' => 'PHONE_EXISTS',
            ])->setStatusCode(400));
        } 

        return true;
    }

    public function hashingPassword($password)
    {
        $hashPassword = $this->repository->hashingPassword($password);

        if(!$hashPassword) {
            throw new HttpResponseException(response()->json([
                'error' => 'PASSWOR_GENERATE_FAIL',
            ])->setStatusCode(400));
        }

        return $hashPassword;
    }

    public function generateUuid()
    {
        $token = $this->repository->generateTokenUuid();

        if(!$token) {
            throw new HttpResponseException(response()->json([
                'error' => 'TOKEN_GENERATE_FAIL',
            ])->setStatusCode(400));
        }

        return $token;
    }
}