<?php

namespace App\Http\Middleware\Microservices\Payment;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MultiplePaymentMethodsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'idem:' . $request->input('idempotency_key');
        if (Cache::has($key)) {
            return response()->json([
                'status'  => 'duplicated',
                'message' => 'Payment already processed for this idempotency key.'
            ], 409);
        }
        Cache::add($key, true, now()->addMinutes(5));
        return $next($request);
    }
}
