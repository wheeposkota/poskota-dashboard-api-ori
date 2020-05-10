<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostMeta extends Model
{
    protected $table = 'postmeta';
    protected $primaryKey = 'id_postmeta';

    const KEY_COVER_IMAGE = 'cover_image';
    const KEY_VIEW_COUNT = 'view_count';
    const KEY_METAS = [
        'meta_title',
        'meta_keyword',
        'meta_description'
    ];
    const IMAGE_SIZE_ORIGINAL = 'file';
    const IMAGE_SIZE_MEDIUM = 'medium';
    const IMAGE_SIZE_SMALL = 'small';
    const IMAGE_SIZE_THUMBNAIL = 'thumb';
    const IMAGE_CAPTION = 'thumbnail';
    const IMAGE_META = 'meta';

    protected $fillable = [
        'post_id',
        'meta_key',
        'meta_value',
    ];

    protected $casts = [
        'meta_value' => 'array'
    ];

}
