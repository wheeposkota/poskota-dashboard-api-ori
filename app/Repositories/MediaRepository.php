<?php

namespace App\Repositories;

use App\Models\Media;
use App\Services\Generic;
use App\Services\Cache;
use Illuminate\Support\Facades\DB;

class MediaRepository extends Repository
{
    public $media;

    public function __construct(Media $media)
    {
        $this->media = $media;
    }

    public function retrieveMediaDetail($param)
    {
        $keyCache = $this->keyBuilder(self::MODULE_MEDIA_DETAIL, $param);
        if (Cache::has($keyCache))
            return Cache::get($keyCache);

        $select = [
            'id_gallery',
            'gallery_title',
            'gallery_slug',
            'gallery_content',
            'gallery_type',
            'gallery_source',
            'gallery.created_at',
            DB::raw('users.name AS author'),
        ];

        $query = $this->media
            ->select($select)
            ->where('gallery_status', Media::STATUS_PUBLISH)
            ->leftJoin('users', function ($q) {
                $q->on('gallery.created_by', '=', 'users.id');
            })
            ->latest();

        if ($param['id']) {
            $result = $query->findOrFail($param['id']);
        } elseif ($param['slug']) {
            $result = $query->where('gallery_slug', $param['slug'])->firstOrFail();
        } else {
            return null;
        }

        Cache::put($keyCache, $result);

        return $result;
    }

    public function retrieveMediaList($param)
    {
        $keyCache = $this->keyBuilder(self::MODULE_MEDIA_LIST, $param);
        if (Cache::has($keyCache))
            return Cache::get($keyCache);

        $select = [
            'id_gallery',
            'gallery_title',
            'gallery_slug',
            'gallery_content',
            'gallery_type',
            'gallery_source',
            'gallery.created_at',
            DB::raw('users.name AS author'),
        ];

        $query = $this->media
            ->select($select)
            ->where('gallery_type', $param['type'])
            ->where('gallery_status', Media::STATUS_PUBLISH)
            ->leftJoin('users', function ($q) {
                $q->on('gallery.created_by', '=', 'users.id');
            })
            ->latest();

        if ($param['search']) {
            $query = $query->where('gallery_title', 'like', '%' . $param['search'] . '%');
        }

        $count = $this->queryCount($query);
        $result = $this->queryRows($query, $param['page'], $param['limit']);

        $result = Generic::paginateQuery($param, $count, $result);

        Cache::put($keyCache, $result);

        return $result;
    }

}
