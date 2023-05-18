<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'code' => $this->code,
            'name' => $this->first_name . ' ' . $this->last_name,
            'phone_number' => $this->phone_number,
            'email' => $this->email,
            'address' => $this->address,
            'city' => $this->city,
            'balance' => $this->wallet->balance ?? null,
            'parent_code' => $this->parent->code ?? null,
            'parent_name' => $this->parent->name ?? null,


        ];
    }
}
