<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SalesPushRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sales'                            => ['required', 'array', 'min:1'],
            'sales.*.local_id'                 => ['required', 'string'],
            'sales.*.deleted_at'               => ['nullable', 'integer', 'min:0'],
            'sales.*.customer_id'              => ['nullable', 'integer', 'exists:customers,id'],
            'sales.*.customer_name'            => ['nullable', 'string', 'max:255'],
            'sales.*.total_amount_cents'       => ['required_without:sales.*.deleted_at', 'nullable', 'integer', 'min:0'],
            'sales.*.created_at'               => ['required_without:sales.*.deleted_at', 'nullable', 'integer', 'min:0'],
            'sales.*.items'                    => ['required_without:sales.*.deleted_at', 'nullable', 'array', 'min:1'],
            'sales.*.items.*.product_id'       => ['required', 'integer', 'exists:products,id'],
            'sales.*.items.*.unit_price_cents' => ['required', 'integer', 'min:0'],
            'sales.*.items.*.quantity'         => ['required', 'integer', 'min:1'],
            'sales.*.items.*.subtotal_cents'   => ['required', 'integer', 'min:0'],
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422),
        );
    }
}
