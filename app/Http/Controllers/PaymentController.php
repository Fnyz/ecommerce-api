<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PaymentService;
use App\Models\Order;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $paymentService){}

    public function checkout(Request $request, Order $order)
    {
        abort_if($order->user_id !== $request->user()->id, 403, 'Unauthorized');
        abort_if($order->payment_status === 'paid', 400, 'Order already paid');

        // Create a payment intent using the PaymentService
        $url = $this->paymentService->createCheckoutSession($order);

        // just return the checkout url to the frontend, frontend will handle the redirect to stripe checkout page
        return response()->json(['checkout_url' => $url]);
    }

    // Handle the Stripe webhook for payment events
    public function webhook(Request $request)
    {
        $this->paymentService->handleWebhook(
            $request->getContent(),
            $request->header('Stripe-Signature')
        );

        return response()->json(['message' => 'Webhook handled successfully and Successfully updated the order payment status']);
    }
}
