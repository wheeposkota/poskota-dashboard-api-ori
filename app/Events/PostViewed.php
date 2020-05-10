<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostViewed implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $post_id;

    public function __construct($post_id)
    {
        $this->post_id = $post_id;
    }
}
