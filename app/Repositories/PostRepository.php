<?php

namespace App\Repositories;

use App\Models\Post;
use App\Models\PostBookmark;
use App\Models\PostMeta;
use App\Models\PostRelated;
use App\Models\PostRelationship;
use App\Models\PostTerm;
use App\Models\PostTermTaxo;
use App\Services\Generic;
use App\Services\Cache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostRepository extends Repository
{
    const TYPE_LATEST = 'latest';
    const TYPE_HEADLINE = 'headline';
    const TYPE_POPULAR = 'popular';
    const TYPE_RECOMMENDATION = 'recommendation';

    public $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function getTypePost()
    {
        return [
            self::TYPE_LATEST,
            self::TYPE_HEADLINE,
            self::TYPE_POPULAR,
            self::TYPE_RECOMMENDATION,
        ];
    }

    public function retrieveCategory($param)
    {
        $keyCache = $this->keyBuilder(self::MODULE_CATEGORY, $param);
        if (Cache::has($keyCache))
            return Cache::get($keyCache);

        $sub = DB::raw('(
            select count(id_term_relationships)
            from term_relationships
            where term_taxonomy_id = term_taxonomy.id_term_taxonomy
        ) as content_count');

        $select = [
            'id_term_taxonomy',
            'name',
            'slug',
            'taxonomy',
            'parent_id',
            $sub,
        ];

        $res = PostTerm::query()
            ->select($select)
            ->leftJoin('term_taxonomy', function ($q) {
                $q->on('term_taxonomy.term_id', '=', 'terms.id_terms');
            })
            ->where('term_taxonomy.taxonomy', PostTermTaxo::TYPE_CATEGORY);

        if ($param['parent'])
            $res = $res->where('parent_id', $param['parent']);

        $result = $res->get()
            // show only category with content
            // ->filter(function($item) {
            //     return $item->content_count > 0;
            // })
            ->values();

        Cache::put($keyCache, $result);

        return $result;
    }

    public function retrievePostList($param)
    {
        $keyCache = $this->keyBuilder(self::MODULE_POST_LIST, $param);
        if (Cache::has($keyCache))
            return Cache::get($keyCache);

        $query = $this->post
            ->where('post_type', Post::TYPE_POST)
            ->where('post_status', Post::STATUS_PUBLISH)
            ->with([
                'meta',
                'topic' => function($q) {
                    return $q->select('term_id', 'taxonomy', 'terms.name', 'parent_id')
                    ->where('taxonomy', '=', PostTermTaxo::TYPE_CATEGORY)
                    ->join('terms', 'terms.id_terms', '=', 'term_taxonomy.term_id');
                }
            ]);

        if ($param['category']) {
            $category = PostTerm::query()
                ->leftJoin('term_taxonomy', function ($q) {
                    $q->on('terms.id_terms', '=', 'term_taxonomy.term_id');
                })
                ->where('term_taxonomy.taxonomy', PostTermTaxo::TYPE_CATEGORY)
                ->where('id_term_taxonomy', $param['category'])
                ->first();
            if ($category) {
                $category_child = PostTermTaxo::query()
                    ->select('id_term_taxonomy')
                    ->where('parent_id', $param['category'])
                    ->get();
                $id_cats = [ $param['category'] ];
                foreach ($category_child as $c) {
                    $id_cats[] = $c['id_term_taxonomy'];
                }

                $query = $query
                    ->leftJoin('term_relationships as category_relation', function ($q) {
                        $q->on('posts.id_posts', '=', 'category_relation.object_id');
                    })
                    ->whereIn('category_relation.term_taxonomy_id', $id_cats);
            }
        }

        if ($param['tag']) {
            $tag = PostTerm::query()
                ->leftJoin('term_taxonomy', function ($q) {
                    $q->on('terms.id_terms', '=', 'term_taxonomy.term_id');
                })
                ->where('term_taxonomy.taxonomy', PostTermTaxo::TYPE_TAG)
                ->where('slug', $param['tag'])
                ->first();
            if ($tag) {
                $query = $query
                    ->leftJoin('term_relationships as tag_relation', function ($q) {
                        $q->on('posts.id_posts', '=', 'tag_relation.object_id');
                    })
                    ->where('tag_relation.term_taxonomy_id', $tag['id_term_taxonomy']);
            }
        }

        if ($param['search']) {
            $query = $query->where('post_title', 'like', '%' . $param['search'] . '%');
        }

        switch ($param['type']) {
            case self::TYPE_HEADLINE:
                $query = $query->where('posts.int_highlight', Post::TURN_ON);
                break;
            case self::TYPE_POPULAR:
                $query = $query->where('posts.int_popular', Post::TURN_ON);
                break;
            case self::TYPE_RECOMMENDATION:
                //todo
                break;
            case self::TYPE_LATEST:
            default:
                break;
        }

        $query = $query
            ->where('publish_date', '<=', Carbon::now()->format('Y-m-d H:i:s'))
            ->latest('publish_date');

        $count = $this->queryCount($query);
        $result = $this->queryRows($query, $param['page'], $param['limit']);

        $result = Generic::paginateQuery($param, $count, $result);

        Cache::put($keyCache, $result);

        return $result;
    }

    private function fetchNewsByIds($ids)
    {
        $select = [
            'id_posts',
            'post_slug',
            'post_title',
            'post_content',
            'publish_date',
            'posts.created_at',
            DB::raw('postmeta.meta_value AS cover'),
            DB::raw('terms.name AS category'),
            'posts.int_highlight',
            'posts.int_popular',
        ];

        if (! is_array($ids))
            $ids = [ $ids ];

        $query = $this->post
            ->select($select)
            ->whereIn($this->post->getKeyName(), $ids)
            ->where('post_type', Post::TYPE_POST)
            ->where('post_status', Post::STATUS_PUBLISH)
            ->where('taxonomy', PostTermTaxo::TYPE_CATEGORY)
            ->where('publish_date', '<=', Carbon::now()->format('Y-m-d H:i:s'))
            ->leftJoin('postmeta', function ($q) {
                $q->on('posts.id_posts', '=', 'postmeta.post_id')
                    ->where('postmeta.meta_key', PostMeta::KEY_COVER_IMAGE);
            })
            ->leftJoin('term_relationships', function ($q) {
                $q->on('posts.id_posts', '=', 'term_relationships.object_id');
            })
            ->leftJoin('term_taxonomy', function ($q) {
                $q->on('term_relationships.term_taxonomy_id', '=', 'term_taxonomy.id_term_taxonomy');
            })
            ->leftJoin('terms', function ($q) {
                $q->on('term_taxonomy.term_id', '=', 'terms.id_terms');
            });

        return $query->get();
    }

    public function retrieveRelated($param)
    {
        $keyCache = $this->keyBuilder(self::MODULE_POST_RELATED, $param);
        if (Cache::has($keyCache))
            return Cache::get($keyCache);

        $id_column = $this->post->getKeyName();

        $prev = $this->post->select($id_column)->where($id_column, '<', $param['id'])->where('publish_date', '<=', Carbon::now()->format('Y-m-d H:i:s'))->orderBy($id_column, 'desc')->first();
        $next = $this->post->select($id_column)->where($id_column, '>', $param['id'])->where('publish_date', '<=', Carbon::now()->format('Y-m-d H:i:s'))->orderBy($id_column, 'asc')->first();

        $exclude_id = [ (int) $param['id'] ];
        if ($prev)
            $exclude_id[] = $prev->getKey();
        if ($next)
            $exclude_id[] = $next->getKey();

        $related = $this->retrieveRelatedPost($param['id'], $param['limit'], $exclude_id);

        $result = [
            'prev_next' => [
                'prev' => $this->fetchNewsByIds($prev[$id_column])[0] ?? null,
                'next' => $this->fetchNewsByIds($next[$id_column])[0] ?? null,
            ],
            'related' => $related,
        ];

        Cache::put($keyCache, $result);

        return $result;
    }

    private function retrieveRelatedPost($id, $limit, $exclude_id)
    {
        $safe_limit = $limit + 10; // case if post got unpublished
        $ids = collect([]);

        //find by related tagged
        PostRelated::select('post_id')->where('post_id', $id)->get()->each(function($i) use ($ids) {
            $ids->push($i->post_id);
        });

        //find by same tag or category
        if ($ids->count() < $safe_limit) {
            $id_tag_category = PostRelationship::select('term_taxonomy_id')->where('object_id', $id)->get()->pluck('term_taxonomy_id');
            PostRelationship::query()
                ->select('object_id')
                ->whereIn('term_taxonomy_id', $id_tag_category)
                ->take($safe_limit)
                ->latest()
                ->get()
                ->map(function($i) use ($ids) {
                    $ids->push($i->object_id);
                });
        }

        //find by latest post
        if ($ids->count() < $safe_limit) {
            Post::query()
                ->select('id_posts')
                ->take($safe_limit)
                ->where('publish_date', '<=', Carbon::now()->format('Y-m-d H:i:s'))
                ->latest()
                ->get()
                ->map(function($i) use ($ids) {
                    $ids->push($i->id_posts);
                });
        }

        $ids = $ids
            ->unique()
            ->filter(function($i) use ($exclude_id) {
                return ! in_array($i, $exclude_id);
            })
            ->values()
            ->toArray();

        $result = $this->fetchNewsByIds($ids);

        return $result->shuffle()->take($limit)->values();
    }

    public function retrievePostDetail($param)
    {
        $keyCache = $this->keyBuilder(self::MODULE_POST_LIST, $param);
        if (Cache::has($keyCache))
            return Cache::get($keyCache);

        $select = [
            'id_posts',
            'post_slug',
            'post_title',
            'post_content',
            'comment_status',
            'publish_date',
            'posts.created_at',
            'posts.updated_at',
            DB::raw('users.name AS author'),
        ];

        $query = $this->post
            ->select($select)
            ->where('post_type', Post::TYPE_POST)
            ->leftJoin('users', function ($q) {
                $q->on('posts.created_by', '=', 'users.id');
            });

        if(! Auth::guard('cms-api')->check()) //if regular api
            $query = $query
                ->where('publish_date', '<=', Carbon::now()->format('Y-m-d H:i:s'))
                ->where('post_status', Post::STATUS_PUBLISH);

        if ($param['id']) {
            $result = $query
                ->findOrFail($param['id']);
        } elseif ($param['slug']) {
            list($year, $month, $date, $slug) = explode('/', $param['slug']);
            $result = $query
                ->where('post_slug', $slug)
                ->firstOrFail();
        } else {
            throw new ModelNotFoundException();
        }

        $select = [
            DB::raw('terms.name AS category'),
            DB::raw('terms.slug AS category_slug'),
            DB::raw('parent_terms.name AS parent_category'),
            DB::raw('parent_terms.slug AS parent_category_slug'),
        ];
        $categories = PostRelationship::query()
            ->select($select)
            ->where('object_id', $result->getKey())
            ->where('term_taxonomy.taxonomy', PostTermTaxo::TYPE_CATEGORY)
            ->leftJoin('term_taxonomy', function ($q) {
                $q->on('term_relationships.term_taxonomy_id', '=', 'term_taxonomy.id_term_taxonomy');
            })
            ->leftJoin('terms', function ($q) {
                $q->on('term_taxonomy.term_id', '=', 'terms.id_terms');
            })
            ->leftJoin('term_taxonomy as parent_term_taxonomy', function ($q) {
                $q->on('term_taxonomy.parent_id', '=', 'parent_term_taxonomy.id_term_taxonomy');
            })
            ->leftJoin('terms as parent_terms', function ($q) {
                $q->on('parent_term_taxonomy.term_id', '=', 'parent_terms.id_terms');
            })
            ->first();

        $result->category = $categories->category ?? "";
        $result->category_slug = $categories->category_slug ?? "";
        $result->parent_category = $categories->parent_category ?? "";
        $result->parent_category_slug = $categories->parent_category_slug ?? "";

        $select = [
            'meta_key',
            'meta_value',
        ];
        $result->meta = PostMeta::select($select)->where('post_id', $result->getKey())->get()->toArray();

        $select = [
            'slug',
            'name',
        ];
        $result->tags = PostRelationship::query()
            ->select($select)
            ->where('object_id', $result->getKey())
            ->leftJoin('term_taxonomy', function ($q) {
                $q->on('term_relationships.term_taxonomy_id', '=', 'term_taxonomy.id_term_taxonomy')
                    ->where('taxonomy', PostTermTaxo::TYPE_TAG);
            })
            ->leftJoin('terms', function ($q) {
                $q->on('term_taxonomy.term_id', '=', 'terms.id_terms');
            })
            ->get();

        Cache::put($keyCache, $result);

        return $result;
    }

    public function retrieveOtherPage($param = [])
    {
        $keyCache = $this->keyBuilder(self::MODULE_OTHER_PAGE, $param);
        if (Cache::has($keyCache))
            return Cache::get($keyCache);

        $select = [
            'post_slug',
            'post_title',
            'post_content',
            'posts.created_at',
        ];

        $result = $this->post
            ->select($select)
            ->where('post_type', Post::TYPE_PAGE)
            ->where('post_status', Post::STATUS_PUBLISH)
            ->get();

        Cache::put($keyCache, $result);

        return $result;
    }

    public function detailOtherPage($param)
    {
        $keyCache = $this->keyBuilder(self::MODULE_OTHER_PAGE_DETAIL, $param);
        if (Cache::has($keyCache))
            return Cache::get($keyCache);

        $select = [
            'post_slug',
            'post_title',
            'post_content',
            'posts.created_at',
        ];

        $result = $this->post
            ->select($select)
            ->where('post_type', Post::TYPE_PAGE)
            ->where('post_status', Post::STATUS_PUBLISH)
            ->where('post_slug', $param['link'])
            ->firstOrFail();

        Cache::put($keyCache, $result);

        return $result;
    }

    public function retrieveBookmarkList($param)
    {
        $select = [
            DB::raw('object_id as id'),
        ];

        $query = PostBookmark::query()
            ->select($select)
            ->where('member_id', $param['user_id'])
            ->where('post_type', Post::TYPE_POST)
            ->where('post_status', Post::STATUS_PUBLISH)
            ->where('publish_date', '<=', Carbon::now()->format('Y-m-d H:i:s'))
            ->leftJoin('posts', function ($q) {
                $q->on('post_bookmarks.object_id', '=', 'posts.id_posts');
            });

        $query = $query->latest('post_bookmarks.created_at');

        $count = $this->queryCount($query);
        $result = $this->queryRows($query, $param['page'], $param['limit']);

        $result = Generic::paginateQuery($param, $count, $result);

        $post = $this->fetchNewsByIds( $result['list_data']->pluck('id')->toArray() );

        $result['list_data'] = $result['list_data']->map(function($item) use ($post) {
            return $post->where('id_posts', $item->id)->first();
        });

        return $result;
    }

    public function submitBookmark($param)
    {
        $data = [
            'object_id' => $param['id'],
            'member_id' => $param['user_id'],
        ];

        $check = PostBookmark::query()
            ->where('object_id', $data['object_id'])
            ->where('member_id', $data['member_id'])
            ->first();
        if ($check) {
            $result = 'unbookmark';
            $check->delete();
        } else {
            $result = 'bookmark';
            PostBookmark::create($data);
        }

        return $result;
    }
}
