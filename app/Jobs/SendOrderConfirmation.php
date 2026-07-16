<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmation implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        Log::info('Sending order confirmation', ['order_id' => $this->order->id]);

        // For now, log instead of actually mailing (we'll wire up Mail properly if needed)
        Log::info("Order confirmation for {$this->order->order_number} sent to user #{$this->order->user_id}");

        // Once you have a Mailable set up:
        // Mail::to($this->order->user->email)->send(new OrderConfirmationMail($this->order));
    }
}
