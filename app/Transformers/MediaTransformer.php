<?php

namespace App\Transformers;

use App\Models\Media;
use Illuminate\Database\Eloquent\Collection;

class MediaTransformer extends Transformer
{
    public function detailMedia(Media $item)
    {
        $sources = [];

        if ($item->gallery_type == Media::TYPE_VIDEO) {
            foreach ($item->gallery_source as $s)
                $sources[] = [
                    'code' => $s['video'],
                    'link' => sprintf('https://www.youtube.com/watch?v=%s', $s['video']),
                    'thumbnail' => sprintf('https://img.youtube.com/vi/%s/hqdefault.jpg', $s['video']),
                ];
        }

        if ($item->gallery_type == Media::TYPE_PHOTO) {
            foreach ($item->gallery_source as $s) {
                if ($s['photo'][0] == '/')
                    $s['photo'] = substr($s['photo'], 1);
                $sources[] = generate_cdn($s['photo']);
            }
            if (count($sources) == 0)
                $sources[] = no_pic('news');
        }

        return [
            'id' => $item->id_gallery,
            'title' => $item->gallery_title,
            'slug' => $item->gallery_slug,
            'content' => set_asset_link($item->gallery_content),
            'type' => $item->gallery_type,
            'source' => $sources,
            'author' => $item->author,
            'publish_date' => dateid($item->created_at),
            'publish_date_raw' => $item->created_at->toDatetimeString(),
        ];
    }

    public function listMedia(Collection $data)
    {
        return $data->map(function ($item) {
            $sources = [];

            if ($item->gallery_type == Media::TYPE_VIDEO) {
                foreach ($item->gallery_source as $s)
                    $sources[] = [
                        'link' => sprintf('https://www.youtube.com/watch?v=%s', $s['video']),
                        'thumbnail' => sprintf('https://img.youtube.com/vi/%s/hqdefault.jpg', $s['video']),
                    ];
            }

            if ($item->gallery_type == Media::TYPE_PHOTO) {
                foreach ($item->gallery_source as $s) {
                    if ($s['photo'][0] == '/')
                        $s['photo'] = substr($s['photo'], 1);
                    $sources[] = generate_cdn($s['photo']);
                }
                if (count($sources) == 0)
                    $sources[] = no_pic('news');
            }

            return [
                'id' => $item->id_gallery,
                'title' => $item->gallery_title,
                'slug' => $item->gallery_slug,
                'content' => $item->gallery_content,
                'type' => $item->gallery_type,
                'source' => $sources,
                'author' => $item->author,
                'publish_date' => dateid($item->created_at),
                'publish_date_raw' => $item->created_at->toDatetimeString(),
            ];
        });
    }
}