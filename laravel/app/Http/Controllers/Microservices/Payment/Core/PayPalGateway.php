<?php
namespace App\Http\Controllers\Microservices\Payment\Core;

use App\Http\Controllers\Microservices\Payment\Core\PaymentGatewayInterface;

class PayPalGateway implements PaymentGatewayInterface
{
    private string $baseUrl;
    private string $clientId;
    private string $clientSecret;

    public function __construct()
    {
        //TODO: attach the required .env vars to the config/services.php file
        $this->clientId     = config('services.paypal.client_id');
        $this->clientSecret = config('services.paypal.secret');
        $this->baseUrl      = config('services.paypal.mode') === 'live'
                                ? 'https://api-m.paypal.com'
                                : 'https://api-m.sandbox.paypal.com';
    }
    //& ---> Bring access token from paypal API via passing clientId & clientSecret 
    //& ---> Use this token to make a payment order to bring the payment url
        private function accessToken(): string
        {
            $response = Http::asForm()->withBasicAuth(
                $this->clientId,
                $this->clientSecret
            )->post("{$this->baseUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials'
            ]);

            return $response->json()['access_token'];
        }

        public function createOrder(float $amount): array
            {
                $token = $this->accessToken();

                $response = Http::withToken($token)
                    ->post("{$this->baseUrl}/v2/checkout/orders", [
                        'intent' => 'CAPTURE',
                        'purchase_units' => [[
                            'amount' => [
                                'currency_code' => 'USD',
                                'value'         => $amount,
                            ],
                        ]],
                    ]);

                return $response->json();
            }
    //TODO: still in progress . . . .
        public function initiate(array $paymentData): array
        {
            // PayPal specific implementation
            $totalAmount = $paymentData['amount'] * $paymentData['quantity'];
            
            // Simulate PayPal API call
            $paypalResponse = $this->callPayPalAPI([
                'amount' => $totalAmount,
                'currency' => 'USD',
                'description' => $paymentData['description'],
                'return_url' => $paymentData['return_url'],
                'cancel_url' => $paymentData['cancel_url'],
            ]);

            return [
                'status' => 'pending',
                'gateway' => 'paypal',
                'transaction_id' => $paypalResponse['transaction_id'] ?? 'PP-' . uniqid(),
                'redirect_url' => $paypalResponse['approval_url'] ?? 'https://paypal.com/checkout',
                'message' => 'Redirect to PayPal to complete payment',
            ];
        }
        
        //TODO: build the process PayPal callback/webhook
        public function processCallback(array $callbackData): array
        {
            return [
                'status' => 'success',
                'transaction_id' => $callbackData['transaction_id'],
                'paid_amount' => $callbackData['amount'],
            ];
        }

        //TODO: Build the verify payment with PayPal API
        public function verifyPayment(string $transactionId): bool
        {
            return true;
        }

        //TODO: Actual PayPal API integration would go here
        private function callPayPalAPI(array $data): array
        {
            return [
                'transaction_id' => 'PP-' . uniqid(),
                'approval_url' => 'https://paypal.com/checkout/approve',
            ];
        }
}