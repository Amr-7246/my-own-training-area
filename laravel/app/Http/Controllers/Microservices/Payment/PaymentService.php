<?php
namespace App\Http\Controllers\Microservices\Payment;

use App\Http\Controllers\Microservices\Payment\ProductValidator;
use App\Http\Controllers\Microservices\Payment\PaymentGatewayResolver;
//& Orchestration class
class PaymentService
{
    private ProductValidator $productValidator;
    private PaymentGatewayResolver $gatewayResolver;

    public function __construct()
    {
        $this->productValidator = new ProductValidator();
        $this->gatewayResolver = new PaymentGatewayResolver();
    }

    public function initiatePayment(array $paymentData): array
    {
        //& Validate the product
        $validation = $this->productValidator->validate(
            $paymentData['product_id'],
            $paymentData['product_amount']
        );

        if (!$validation['valid']) {
            return $validation['error'];
        }

        $product = $validation['product'];

        //& Resolve the payment gateway
        try {
            $gateway = $this->gatewayResolver->resolve($paymentData['payment_method']);
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'code' => 400,
            ];
        }

        //& Prepare payment data
        $paymentInfo = [
            'amount' => $product->price,
            'quantity' => $paymentData['product_amount'],
            'payment_method' => $paymentData['payment_method'],
            'description' => "Payment for product #{$product->id}",
            'return_url' => url('/payment/success'),
            'cancel_url' => url('/payment/cancel'),
            'billing_data' => $paymentData['billing_data'] ?? [],
        ];

        //& Initiate payment with the gateway
        $paymentResponse = $gateway->initiate($paymentInfo);

        //& Store transaction in database (still in inprogress)
        $this->storeTransaction([
            'product_id' => $product->id,
            'amount' => $product->price * $paymentData['product_amount'],
            'payment_method' => $paymentData['payment_method'],
            'gateway' => $paymentResponse['gateway'],
            'transaction_id' => $paymentResponse['transaction_id'],
            'status' => $paymentResponse['status'],
        ]);

        return $paymentResponse;
    }
    //TODO: Store transaction in database
    private function storeTransaction(array $transactionData): void
    {
        // Transaction::create($transactionData);
    }
}