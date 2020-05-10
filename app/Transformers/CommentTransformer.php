<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Collection;

class CommentTransformer extends Transformer
{
    public function listComment(Collection $data)
    {
        return $data->map(function ($item) {
            if ($item->avatar == 'nopic.png') $item->avatar = 'member_avatar/nouser.png';

            return [
                'name' => $item->name,
                'email' => $item->email,
                'avatar' => generate_cdn($item->avatar),
                'comment' => $item->comment,
                'created_at' => dateid($item->created_at),
                'created_at_raw' => $item->created_at->toDatetimeString(),
            ];
        });
    }
}
