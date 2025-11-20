<?php
namespace App\Http\Controllers\Microservices\Payment;

//~ Registry/configuration class
class SupportedPaymentMethods
{
    // Define all supported methods and their gateway mappings
    private static array $methods = [
        'universal_card' => [
            'gateway' => 'paypal',
            'label' => 'Universal Card',
            'enabled' => true,
        ],
        'visa' => [
            'gateway' => 'paymob',
            'label' => 'Visa Card',
            'enabled' => true,
        ],
        'mastercard' => [
            'gateway' => 'paymob',
            'label' => 'Mastercard',
            'enabled' => true,
        ],
        'fawry' => [
            'gateway' => 'paymob',
            'label' => 'Fawry',
            'enabled' => true,
        ],
        'wallet' => [
            'gateway' => 'paymob',
            'label' => 'Mobile Wallet',
            'enabled' => true,
        ],
        'local_card' => [
            'gateway' => 'paymob',
            'label' => 'Local Card',
            'enabled' => true,
        ],
        'vodafone_cash' => [
            'gateway' => 'paymob',
            'label' => 'Vodafone Cash',
            'enabled' => true,
        ],
    ];

    // Check if a payment method is supported
    public static function isSupported(string $method): bool
    {
        return isset(self::$methods[$method]) && self::$methods[$method]['enabled'];
    }

    // Get the gateway for a specific payment method
    public static function getGateway(string $method): ?string
    {
        return self::$methods[$method]['gateway'] ?? null;
    }

    // Get all supported methods
    public static function all(): array
    {
        return array_filter(self::$methods, fn($method) => $method['enabled']);
    }

    // Get method details
    public static function getMethodDetails(string $method): ?array
    {
        return self::$methods[$method] ?? null;
    }
}
