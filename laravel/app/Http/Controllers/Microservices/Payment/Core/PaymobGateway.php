<?php
namespace App\Http\Controllers\Microservices\Payment\Core;
use App\Http\Controllers\Microservices\Payment\Core\PaymentGatewayInterface;

class PaymobGateway implements PaymentGatewayInterface
{
    private string $apiKey;
    private string $baseUrl;
    private string $iframeId;

    public function __construct()
    {
        //TODO: attach the required .env vars to the config/services.php file
        $this->apiKey  = config('services.paymob.api_key');
        $this->baseUrl = 'https://accept.paymob.com/api';
        $this->iframeId = config('services.paymob.iframe_id');
    }
    //& --Authanticat with the apiKey to bring token ---> register the payment order to bring the orderID
    //& --Use Token + orderId + billingData + integrationId (which determin the payment method) to bring the paymentKey
    //& --Finally generate the payment URL using the (paymentKey + iframeId) and pass it back to the frontend

        private function authenticate(): string
        {
            $response = Http::post("{$this->baseUrl}/auth/tokens", [
                "api_key" => $this->apiKey
            ]);

            return $response->json()['token'];
        }

        private function registerOrder(string $token, float $amount): int
        {
            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/ecommerce/orders", [
                    "amount_cents" => intval($amount * 100),
                    "currency" => "EGP",
                    "items" => []
                ]);

            return $response->json()['id'];
        }


        private function generatePaymentKey(string $token, int $orderId, float $amount, string $method): string
        {
            //TODO: attach the required .env vars to the config/services.php file
            $integrationIds = [
                'card'   => config('services.paymob.card_integration_id'),
                'wallet' => config('services.paymob.wallet_integration_id'),
                'kiosk'  => config('services.paymob.kiosk_integration_id'),
            ];

            $response = Http::withToken($token)
                ->post("{$this->baseUrl}/acceptance/payment_keys", [
                    "amount_cents" => intval($amount * 100),
                    "expiration"   => 3600,
                    "order_id"     => $orderId,
                    //TODO:extract billing_data from the user table via user id 
                    "billing_data" => [
                        "first_name" => "Amr",
                        "last_name"  => "Customer",
                        "email"      => "example@mail.com",
                        "phone_number" => "+201001234567",
                    ],
                    "currency" => "EGP",
                    "integration_id" => $integrationIds[$method],
                ]);

            return $response->json()['token'];
        }


        public function generateIframeUrl(float $amount, string $method): string
        {
            $authToken = $this->authenticate();
            $orderId   = $this->registerOrder($authToken, $amount);
            $paymentKey = $this->generatePaymentKey($authToken, $orderId, $amount, $method);

            return "https://accept.paymob.com/api/acceptance/iframes/{$this->iframeId}?payment_token={$paymentKey}";
        }

    //TODO: still in progress . . . .
        public function initiate(array $paymentData): array
        {
            // Paymob specific implementation
            $totalAmount = $paymentData['amount'] * $paymentData['quantity'];
            
            // Simulate Paymob API call
            $paymobResponse = $this->callPaymobAPI([
                'amount' => $totalAmount * 100, // Paymob uses cents
                'currency' => 'EGP',
                'payment_method' => $paymentData['payment_method'],
                'billing_data' => $paymentData['billing_data'] ?? [],
            ]);

            return [
                'status' => 'pending',
                'gateway' => 'paymob',
                'transaction_id' => $paymobResponse['transaction_id'] ?? 'PMB-' . uniqid(),
                'redirect_url' => $paymobResponse['iframe_url'] ?? 'https://paymob.com/checkout',
                'message' => 'Redirect to Paymob to complete payment',
            ];
        }

        public function processCallback(array $callbackData): array
        {
            // Process Paymob callback/webhook
            return [
                'status' => 'success',
                'transaction_id' => $callbackData['transaction_id'],
                'paid_amount' => $callbackData['amount'] / 100,
            ];
        }

        public function verifyPayment(string $transactionId): bool
        {
            // Verify payment with Paymob API
            return true;
        }

        private function callPaymobAPI(array $data): array
        {
            // Actual Paymob API integration would go here
            return [
                'transaction_id' => 'PMB-' . uniqid(),
                'iframe_url' => 'https://paymob.com/iframe/checkout',
            ];
        }
}
