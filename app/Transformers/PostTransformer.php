<?php

namespace App\Transformers;

use App\Models\Post;
use App\Models\PostMeta;
use App\Models\PostBookmark;
use App\Models\PostTermTaxo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PostTransformer extends Transformer
{
    public function listPost(Collection $data)
    {
        return $data->map(function ($item) {

            $category = $item->topic->sortByDesc('parent_id')->first()->name ?? "";
            $cover_pic = no_pic('news');
            $cover_pic_small = no_pic('news');
            $cover_caption = "";

            // file > thumb > small
            foreach($item->meta as $m) {
                if ($m->meta_key == PostMeta::KEY_COVER_IMAGE) {
                    $cover = $m->meta_value;
                    if (isset($cover[ PostMeta::IMAGE_SIZE_ORIGINAL ]))
                        $cover_pic = generate_cdn($cover[ PostMeta::IMAGE_SIZE_ORIGINAL ]);
                    if (isset($cover[ PostMeta::IMAGE_SIZE_THUMBNAIL ]))
                        $cover_pic = generate_cdn($cover[ PostMeta::IMAGE_SIZE_THUMBNAIL ]);
                    $cover_pic_small = $cover_pic;
                    if (isset($cover[ PostMeta::IMAGE_SIZE_SMALL ]))
                        $cover_pic_small = generate_cdn($cover[ PostMeta::IMAGE_SIZE_SMALL ]);
                    $cover_caption = $cover['caption'] ?? "";
                }
            }

            $title = strip_tags($item->post_title);
            $title_preview = get_words($title, 10);
            if ($title != $title_preview)
                $title_preview .= '...';
            if ($title_preview == '...')
                $title_preview = $title;

            return [
                'id' => $item->getKey(),
                'link' => $item->link,
                'title' => $title_preview,
                'title_full' => $title,
                'preview' => get_excerpt($item->post_content),
                'category' => $category,
                'cover' => $cover_pic,
                'cover_small' => $cover_pic_small,
                'cover_caption' => $cover_caption,
                'publish_date' => dateid($item->publish_date),
                'publish_date_raw' => $item->publish_date->toDatetimeString(),
            ];
        });
    }

    public function relatedPost($data)
    {
        return [
            'next' => $this->transformLitePost($data['prev_next']['next']),
            'prev' => $this->transformLitePost($data['prev_next']['prev']),
            'related' => $data['related']->map(function($item) {
                return $this->transformLitePost($item);
            }),
        ];
    }

    public function bookmarkPost($data)
    {
        return $data->map(function ($item) {
            return $this->transformLitePost($item);
        });
    }

    private function transformLitePost($item)
    {
        if (is_null($item))
            return [
                'id' => 0,
                'link' => '',
                'title' => '',
                'preview' => '',
                'category' => '',
                'cover' => no_pic('news'),
                'cover_small' => no_pic('news'),
                'cover_caption' => '',
                'publish_date' => '',
                'publish_date_raw' => '',
            ];

        $cover = json_decode($item->cover, true);
        $cover_pic = no_pic('news');
        if (isset($cover[ PostMeta::IMAGE_SIZE_ORIGINAL ]))
            $cover_pic = generate_cdn($cover[ PostMeta::IMAGE_SIZE_ORIGINAL ]);
        if (isset($cover[ PostMeta::IMAGE_SIZE_THUMBNAIL ]))
            $cover_pic = generate_cdn($cover[ PostMeta::IMAGE_SIZE_THUMBNAIL ]);
        $cover_pic_small = $cover_pic;
        if (isset($cover[ PostMeta::IMAGE_SIZE_SMALL ]))
            $cover_pic_small = generate_cdn($cover[ PostMeta::IMAGE_SIZE_SMALL ]);
        $cover_caption = $cover['caption'] ?? "";

        return [
            'id' => $item->getKey(),
            'link' => $item->link,
            'title' => $item->post_title,
            'preview' => get_excerpt($item->post_content),
            'category' => (string) $item->category,
            'cover' => $cover_pic,
            'cover_small' => $cover_pic_small,
            'cover_caption' => $cover_caption,
            'publish_date' => dateid($item->publish_date),
            'publish_date_raw' => $item->publish_date->toDatetimeString(),
        ];
    }

    public function detailPost(Post $data)
    {
        $cover_pic = no_pic('news');
        $cover_caption = "";
        $view_count = 0;
        $meta = [];

        foreach ($data->meta as $m) {
            if ($m['meta_key'] == PostMeta::KEY_COVER_IMAGE) {
                if (isset($m['meta_value']['file']))
                    $cover_pic = generate_cdn($m['meta_value']['file']);
                $cover_caption = $m['meta_value']['caption'] ?? "";
            }
            if ($m['meta_key'] == PostMeta::KEY_VIEW_COUNT) {
                $view_count = (int)$m['meta_value'];
            }
            if (in_array($m['meta_key'], PostMeta::KEY_METAS) && !is_null($m['meta_value'])) {
                $meta[$m['meta_key']] = $m['meta_value'];
            }
        }

        if (! isset($meta['meta_title']))
            $meta['meta_title'] = $data->post_title;
        if (! isset($meta['meta_keyword']))
            $meta['meta_keyword'] = make_meta_keyword($data->post_title);
        if (! isset($meta['meta_description']))
            $meta['meta_description'] = get_excerpt($data->post_content, false);

        $tags = [];
        foreach ($data->tags as $t) {
            if ($t['slug'])
                $tags[] = $t;
        }

        $title = strip_tags($data->post_title);
        $title_preview = get_words($title, 10);
        if ($title != $title_preview)
            $title_preview .= '...';
        if ($title_preview == '...')
            $title_preview = $title;

        $is_bookmark = (bool) PostBookmark::query()
            ->where('object_id', $data->getKey())
            ->where('member_id', \Auth::id() ?? 0)
            ->count();

        return [
            'id' => $data->getKey(),
            'link' => $data->link,
            'title' => $title_preview,
            'title_full' => $title,
            'category' => (string) $data->parent_category,
            'category_slug' => (string) $data->parent_category_slug,
            'subcategory' => $data->category,
            'subcategory_slug' => $data->category_slug,
            'content' => set_asset_link($data->post_content),
            'cover' => $cover_pic,
            'cover_caption' => $cover_caption,
            'view' => $view_count,
            'meta' => $meta,
            'author' => $data->author,
            'tags' => $tags,
            'is_bookmark' => $is_bookmark,
            'publish_date' => dateid($data->publish_date),
            'publish_date_raw' => $data->publish_date->toDatetimeString(),
        ];
    }

    public function listCategory(Collection $data)
    {
        $ids = $data->pluck('id_term_taxonomy')->toArray();

        $select = [
            'parent_id',
            DB::raw('count(*) as total'),
        ];

        $count_child = PostTermTaxo::query()
            ->select($select)
            ->groupBy('parent_id')
            ->whereIn('parent_id', $ids)
            ->get();

        return $data->map(function ($item) use ($count_child) {
            $count = $count_child->where('parent_id', $item->id_term_taxonomy)->first();

            return [
                'id' => $item->id_term_taxonomy,
                'name' => $item->name,
                'slug' => $item->slug,
                'child' => $count ? $count->total : 0,
            ];
        });
    }

    public function listOtherPage(Collection $data)
    {
        return $data->map(function ($item) {
            return [
                'link' => $item->post_slug,
                'title' => $item->post_title,
                'preview' => get_excerpt($item->post_content),
                'publish_date' => dateid($item->created_at),
                'publish_date_raw' => $item->created_at->toDatetimeString(),
            ];
        });
    }

    public function detailOtherPage(Post $data)
    {
        return [
            'link' => $data->post_slug,
            'title' => $data->post_title,
            'content' => $data->post_content,
            'publish_date' => dateid($data->created_at),
            'publish_date_raw' => $data->created_at->toDatetimeString(),
        ];
    }
}
