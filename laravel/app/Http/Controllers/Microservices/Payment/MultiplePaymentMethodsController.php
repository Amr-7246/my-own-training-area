<?php

namespace App\Http\Controllers\Microservices\Payment;

use App\Http\Controllers\Microservices\Payment\PaymentService;
use App\Http\Requests\Microservices\Payment\MultiplePaymentMethodsRequest;
use App\Http\Controllers\Controller;

//~ Thin Layer - HTTP Only
class MultiplePaymentMethodsController extends Controller
{
    private PaymentService $paymentService;
    
    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }
    
    //& --> Get validated data from request --> Delegate to service layer --> Return appropriate HTTP response
    public function initiatePayment(MultiplePaymentMethodsRequest $request)
    {
        $payload = $request->validated();

        $result = $this->paymentService->initiatePayment($payload);

        $statusCode = $result['code'] ?? 200;
        unset($result['code']);

        return response()->json($result, $statusCode);
    }

    public function getSupportedMethods()
    {
        return response()->json([
            'status' => 'success',
            'methods' => SupportedPaymentMethods::all(),
        ]);
    }

}
