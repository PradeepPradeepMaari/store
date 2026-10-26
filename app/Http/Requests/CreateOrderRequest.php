<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Customer block
            'customer'             => ['required', 'array'],
            'customer.name'        => ['required', 'string', 'max:255'],
            'customer.email'       => ['required', 'email', 'max:255'],

            // Items block
            'items'                => ['required', 'array', 'min:1'],
            'items.*.product_id'   => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity'     => ['required', 'integer', 'min:1'],

            // Payment
            'amount_paid'          => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer.required'           => 'Customer information is required.',
            'customer.name.required'      => 'Customer name is required.',
            'customer.email.required'     => 'Customer email is required.',
            'customer.email.email'        => 'Customer email must be a valid email address.',
            'items.required'              => 'At least one order item is required.',
            'items.min'                   => 'At least one order item is required.',
            'items.*.product_id.required' => 'Each item must have a product_id.',
            'items.*.product_id.exists'   => 'Product with ID :input does not exist.',
            'items.*.quantity.required'   => 'Each item must have a quantity.',
            'items.*.quantity.min'        => 'Item quantity must be at least 1.',
        ];
    }
}
