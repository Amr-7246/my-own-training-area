<?php
namespace App\Http\Controllers\Microservices\Payment\Core;

interface PaymentGatewayInterface
{
    public function initiate(array $paymentData): array;
    public function processCallback(array $callbackData): array;
    public function verifyPayment(string $transactionId): bool;
}