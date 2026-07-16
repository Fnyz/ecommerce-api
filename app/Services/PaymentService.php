<?php

namespace App\Services;
use Stripe\StripeClient;
use App\Jobs\DeductProductStock;
use App\Jobs\SendOrderConfirmation;
use App\Models\Order;

class PaymentService
{
    protected StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    // Create a checkout session for the given order
    public function createCheckoutSession(Order $order): String
    {
        // prepare orders items for stripe checkout session
        $lineItems = $order->items->map(function ($item){
            return [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $item->product_name,
                    ],
                    'unit_amount' => (int) round($item->price * 100), // Stripe expects amount in cents
                ],
                'quantity' => $item->quantity,
            ];
        })->toArray();

        // create a checkout session with Stripe
        $session = $this->stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => 'https://ecommerce-api-o2nl.onrender.com/api/payment/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => 'https://ecommerce-api-o2nl.onrender.com/api/payment/cancel',
            'metadata' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ],
        ]);

        // Update the order with the payment reference (session ID)
        $order->update([
            'payment_reference' => $session->id,
        ]);

        return $session->url;
    }

    // Handle the webhook event from Stripe
    public function handlewebhook(string $payload, string $signature): void
    {
        // Handle the webhook event from Stripe
        $event = \Stripe\Webhook::constructEvent(
            $payload,
            $signature,
            config('services.stripe.webhook_secret')
        );

        if($event->type === 'checkout.session.completed'){
            $session = $event->data->object;
            $orderId = $session->metadata->order_id ?? null;

            if($orderId){
                $order = Order::find($orderId);

                // Update the order status to 'completed' and payment_status to 'paid'
                if($order){

                    $order->update([
                        'status' => 'completed',
                        'payment_status' => 'paid',
                    ]);

                    // Broadcast the order status update event
                    broadcast(new \App\Events\OrderStatusUpdated($order));

                    // Dispatch jobs to deduct product stock and send order confirmation email
                    \Illuminate\Support\Facades\Bus::chain([
                        new DeductProductStock($order),
                        new SendOrderConfirmation($order),
                    ])->dispatch();
                }
            }
        }
    }
}
