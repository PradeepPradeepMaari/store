<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'customer'    => $this->whenLoaded('customer', fn () => [
                'id'    => $this->customer->id,
                'name'  => $this->customer->name,
                'email' => $this->customer->email,
            ]),
            'subtotal'    => (float) $this->subtotal,
            'tax_amount'  => (float) $this->tax_amount,
            'grand_total' => (float) $this->grand_total,
            'items'       => OrderItemResource::collection($this->whenLoaded('items')),
            'created_at'  => $this->created_at->toIso8601String(),
            'updated_at'  => $this->updated_at->toIso8601String(),
        ];
    }
}
