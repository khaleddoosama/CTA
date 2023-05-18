<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'selling_price' => (float) $this->selling_price,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'category' => $this->category,
            'image' => $this->image,
        ];
    }
}
