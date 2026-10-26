<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LowStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'threshold' => ['sometimes', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'threshold.integer' => 'Threshold must be an integer.',
            'threshold.min'     => 'Threshold must be 0 or greater.',
        ];
    }
}
