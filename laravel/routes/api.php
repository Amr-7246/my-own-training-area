<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Microservices\Payment\MultiplePaymentMethodsController;

Route::post("/api/payments/initiate")
    ->middleware('idempotency')
    ->uses([MultiplePaymentMethodsController::class, 'initiatePayment']);

//~ USAGE EXAMPLE
// Example request data
$requestData = [
    'product_id' => 123,
    'product_amount' => 2,
    'payment_method' => 'visa',
    'billing_data' => [
        'email' => 'customer@example.com',
        'phone' => '+201234567890',
    ],
];