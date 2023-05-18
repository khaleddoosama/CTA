<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
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
            'name' => $this->user->first_name . ' ' . $this->user->last_name,
            'code' => $this->user->code,
            'balance' => $this->balance,
            // 'from' => $this->transactions->first()->from_user_id,
            'transactions' => TransactionResource::collection($this->transactions),
            
        ];
    }
}
