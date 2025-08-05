<?php

namespace App\Http\Controllers\V1;

use App\Services\CashfreeService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    protected $cashfree;

    public function __construct(CashfreeService $cashfree)
    {
        $this->cashfree = $cashfree;
    }

    public function createOrder(Request $request)
    {
        $validatedData = $request->validate([
            'order_id' => 'required|string',
            'order_amount' => 'required|numeric',
            'customer_name' => 'required|string',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string',
        ]);

        $orderData = [
            'order_id' => $validatedData['order_id'],
            'order_amount' => $validatedData['order_amount'],
            'order_currency' => 'INR',
            'customer_details' => [
                'customer_name' => $validatedData['customer_name'],
                'customer_email' => $validatedData['customer_email'],
                'customer_phone' => $validatedData['customer_phone'],
            ],
            'return_url' => 'https://your-website.com/payment-response',
        ];

        $response = $this->cashfree->createOrder($orderData);

        return response()->json($response);
    }

    public function getOrderStatus($orderId)
    {
        $response = $this->cashfree->getOrder($orderId);
        return response()->json($response);
    }
}
