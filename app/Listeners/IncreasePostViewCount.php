<?php

namespace App\Listeners;

use App\Events\PostViewed;
use App\Models\PostMeta;
use Illuminate\Support\Facades\DB;

class IncreasePostViewCount
{
    public function handle(PostViewed $event)
    {
        $post_id = $event->post_id;

        DB::transaction(function () use ($post_id) {
            $post_view = PostMeta::query()
                ->where('post_id', $post_id)
                ->where('meta_key', PostMeta::KEY_VIEW_COUNT)
                ->first();
            if (is_null($post_view))
                $post_view = PostMeta::create([
                    'post_id' => $post_id,
                    'meta_key' => PostMeta::KEY_VIEW_COUNT,
                    'meta_value' => 0,
                ]);

            if ($post_view) {
                //todo check for unique visitor
                $post_view->meta_value++;
                $post_view->save();
            }
        });
    }
}
