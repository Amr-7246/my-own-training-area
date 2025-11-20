<?php
namespace App\Http\Controllers\Microservices\Payment;

use App\Http\Controllers\Microservices\Payment\Core\PayPalGateway;
use App\Http\Controllers\Microservices\Payment\Core\PaymobGateway;
use App\Http\Controllers\Microservices\Payment\Core\PaymentGatewayInterface;
use App\Http\Controllers\Microservices\Payment\SupportedPaymentMethods;


class PaymentGatewayResolver
{
    private array $gateways = [];

    public function __construct()
    {
        //& Register available gateways
        $this->gateways['paypal'] = new PayPalGateway();
        $this->gateways['paymob'] = new PaymobGateway();
    }

    public function resolve(string $paymentMethod): PaymentGatewayInterface
    {
        //& Check if method is supported
        if (!SupportedPaymentMethods::isSupported($paymentMethod)) {
            throw new \Exception("Payment method '{$paymentMethod}' is not supported");
        }

        //& Get the gateway name for this method
        $gatewayName = SupportedPaymentMethods::getGateway($paymentMethod);

        //& Return the gateway instance
        if (!isset($this->gateways[$gatewayName])) {
            throw new \Exception("Gateway '{$gatewayName}' is not configured");
        }

        return $this->gateways[$gatewayName];
    }
}