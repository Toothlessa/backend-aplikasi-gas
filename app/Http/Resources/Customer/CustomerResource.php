<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'customer_name'             => $this->customer_name,
            'customer_type'             => $this->customer_type,
            'nik'                       => $this->nik,
            'email'                     => $this->email,
            'address'                   => $this->address,
            'phone'                     => $this->phone,
            'active_flag'               => $this->active_flag,
        ];
    }
}
