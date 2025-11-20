<?php

namespace App\Http\Requests\Microservices\Payment;

use Illuminate\Foundation\Http\FormRequest;

class MultiplePaymentMethodsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exist:products,id'],
            'product_amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'in:visa,mastercard,fawry,wallet,local_card,vodavon_cash,universal_card'],
            'idempotency_key' => ['required', 'string', 'max:64'],
        ];
    }
}
