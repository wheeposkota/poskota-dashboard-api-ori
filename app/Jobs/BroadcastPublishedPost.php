<?php

namespace App\Jobs;

use App\Models\Post;
use App\Services\Firebase;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class BroadcastPublishedPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $title = $this->post->post_title;
        $body = $this->post->post_excerpt;
        $topic = Firebase::TOPIC_NEWS;

        Firebase::sendMessaging($title, $body, $topic);
    }
}
