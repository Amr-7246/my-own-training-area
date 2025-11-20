<?php
namespace App\Http\Controllers\Microservices\Payment;

use App\Models\Microservices\Payment\Product;

class ProductValidator
{
    //& ---> validate product parametars at the database (product->amount,product->purchasable, product->price)

    public function validate(int $productId, int $requestedAmount): array
    {
        // Fetch product from database
        $product = Product::select('id', 'amount', 'price', 'purchasable')
            ->where('id', $productId)
            ->first();

        // Check if product exists
        if (!$product) {
            return [
                'valid' => false,
                'error' => [
                    'status' => 'error',
                    'message' => 'Product not found.',
                    'code' => 404,
                ]
            ];
        }

        // Check stock availability
        if ($product->amount < $requestedAmount) {
            return [
                'valid' => false,
                'error' => [
                    'status' => 'out_of_stock',
                    'message' => "Sorry but there are just {$product->amount} pieces of your product. You can take them for now.",
                    'code' => 422,
                    'available_amount' => $product->amount,
                ]
            ];
        }

        // Check if product is purchasable
        if (!$product->purchasable) {
            return [
                'valid' => false,
                'error' => [
                    'status' => 'invalid',
                    'message' => 'Sorry but this product is not purchasable right now. Try again later or select a new product.',
                    'code' => 422,
                ]
            ];
        }

        // Check if price is valid
        if ($product->price <= 0) {
            return [
                'valid' => false,
                'error' => [
                    'status' => 'invalid_price',
                    'message' => 'Sorry but there is an error with the product price. Please try again.',
                    'code' => 422,
                ]
            ];
        }

        // Product is valid
        return [
            'valid' => true,
            'product' => $product,
        ];
    }
}
