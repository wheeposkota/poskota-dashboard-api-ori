<?php

namespace App\Repositories;

use App\Models\GalleryComment;
use App\Models\PostComment;
use App\Services\Generic;
use App\Services\Cache;
use Illuminate\Support\Facades\DB;

class CommentRepository extends Repository
{
    const TYPE_POST = 'post';
    const TYPE_MEDIA = 'media';

    public function retrieveCommentList($param)
    {
        $keyCache = $this->keyBuilder(self::MODULE_COMMENT_LIST, $param);
        if (Cache::has($keyCache))
            return Cache::get($keyCache);

        $select = [
            'object_id',
            'member_id',
            'members.name',
            'members.email',
            'members.avatar',
            'comment',
        ];

        if ($param['type'] == self::TYPE_POST) {
            $query = new PostComment();
        }
        if ($param['type'] == self::TYPE_MEDIA) {
            $query = new GalleryComment();
        }

        $table = $query->getTable();

        $select[] = DB::raw(sprintf('%s.created_at as created_at', $table));

        $query = $query->query()
            ->select($select)
            ->where('object_id', $param['id'])
            ->leftJoin('members', $table . '.member_id', '=', 'members.id');

        $count = $this->queryCount($query);
        $result = $this->queryRows($query, $param['page'], $param['limit']);

        $result = Generic::paginateQuery($param, $count, $result);

        // todo
        // Cache::tags([self::MODULE_COMMENT_LIST . '-' . $param['type'] . '-' . $param['id']])->put($keyCache, $result, 10);

        return $result;
    }

    public function submitComment($param)
    {
        $data = [
            'object_id' => $param['id'],
            'comment' => $param['comment'],
            'member_id' => $param['user_id'],
        ];

        if ($param['type'] == self::TYPE_POST) {
            $query = new PostComment();
        }
        if ($param['type'] == self::TYPE_MEDIA) {
            $query = new GalleryComment();
        }

        $query = $query->query()->create($data);

        // todo
        // Cache::tags([self::MODULE_COMMENT_LIST . '-' . $param['type'] . '-' . $param['id']])->flush();

        return $query;
    }
}
