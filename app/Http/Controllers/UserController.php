<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use Illuminate\Http\Request;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function checkUsernameExists(string $userName)
    {
        if(User::where("username", $userName)->count()==1){
            throw new HttpResponseException(response([
                "errors"=> [
                    "USERNAME_EXISTS"
                ]
            ], 400));
        }
    }

    public function checkEmailExists(string $email)
    {
        if(User::where("email", $email)->count()==1){
            throw new HttpResponseException(response([
                "errors"=> [
                    "EMAIL_EXISTS"
                ]
            ], 400));
        }
    }

    public function register(UserRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->checkUsernameExists($data["username"]);
        $this->checkEmailExists($data["email"]);

        $user = new User($data);
        $user->password = Hash::make($data['password']);
        $user->token = Str::uuid()->toString();
        $user->expiresIn = 10000;
        $user->save();

        return (new UserResource($user))->response()->setStatusCode(201);
        
    }

    public function login(UserLoginRequest $request): UserResource
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();
        if(!$user || !Hash::check($data['password'], $user->password)){
            throw new HttpResponseException(response([
                "errors" => [
                        "EMAIL_PASSWORD_WRONG"
                ]], 401));
        }

        $user->token = Str::uuid()->toString();
        $user->expiresIn = 10000;
        $user->save();

        return new UserResource($user);
    }

    public function get(Request $request): UserResource
    {
        $user = Auth::user();
        return new UserResource($user);
    }

    public function update(UserUpdateRequest $request): UserResource 
    {
        $data = $request->validated();
        $user = Auth::user();

        if(isset($data['username'])){
            if($data['username'] != $user->username){
                $this->checkUsernameExists($data['username']);
            }
            $user->username = $data['username'];    
        }

        if(isset($data['password'])){
            $user->password = Hash::make($data['password']);
        }    
        
        if(isset($data['fullname'])){
            $user->fullname = $data['fullname'];
        }

        if(isset($data['email'])){
            if($data['email'] != $user->email){
                $this->checkEmailExists($data['email']);
            }
            $user->email = $data['email'];
        }

        if(isset($data['phone'])){
            $user->phone = $data['phone'];
        }
        
        if(isset($data['street'])){
            $user->street = $data['street'];
        }

        if(isset($data['city'])){
            $user->city = $data['city'];
        }

        if(isset($data['province'])){
            $user->province = $data['province'];
        }

        if(isset( $data['postal_code'])){
            $user->postal_code = $data['postal_code'];
        }

        if(isset( $data['country'])){
            $user->country = $data['country'];
        }
        
        $user->save();
        return new UserResource($user);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = Auth::user();
        $user->token = null;
        $user->save();

        return response()->json([
            "data" => true
        ])->setStatusCode(200);
    }
}