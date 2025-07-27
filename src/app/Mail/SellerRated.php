<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class SellerRated extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $rating;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Order $order, int $rating)
    {
        $this->order = $order;
        $this->rating = $rating;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('あなたの商品が評価されました')
                    ->view('emails.seller_rated');
    }
}
