<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_phone' => $this->customer_phone,
            'customer_address' => $this->customer_address,
            'customer_city' => $this->customer_city,
            'order_details' => $this->order_details,
            'payment_method' => $this->payment_method,
            'total_amount' => $this->total_amount,
            'discount' => $this->discount,
            'order_status' => $this->order_status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'products' => ProductOrderResource::collection($this->products),
        ];
    }
}
