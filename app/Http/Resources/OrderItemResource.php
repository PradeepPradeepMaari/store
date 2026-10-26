<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'product_id'     => $this->product_id,
            'product_name'   => $this->whenLoaded('product', fn () => $this->product->name),
            'product_code'   => $this->whenLoaded('product', fn () => $this->product->code),
            'quantity'       => $this->quantity,
            'unit_price'     => (float) $this->unit_price,
            'tax_percentage' => (float) $this->tax_percentage,
            'subtotal'       => (float) $this->subtotal,
            'tax_amount'     => (float) $this->tax_amount,
            'total'          => (float) $this->total,
        ];
    }
}
