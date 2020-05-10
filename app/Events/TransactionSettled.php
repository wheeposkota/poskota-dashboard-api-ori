<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionSettled implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order_id;

    public function __construct($order_id)
    {
        $this->order_id = $order_id;
    }
}
