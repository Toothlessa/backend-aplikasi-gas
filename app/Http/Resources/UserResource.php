<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Foundation\Http\FormRequest;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' =>$this->email,
            // 'fullname' =>$this->fullname,
            'token' =>$this->whenNotNull($this->token),
            'expiresIn'=>$this->expiresIn,
            // 'phone' =>$this->phone,
            // 'street' =>$this->street,
            // 'city' =>$this->city,
            // 'province' =>$this->province,
            // 'postal_code' =>$this->postal_code,
            // 'country' =>$this->country,
        ];
    }

}
