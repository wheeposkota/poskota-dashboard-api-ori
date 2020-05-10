<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';
    protected $primaryKey = 'id_posts';
    protected $appends = ['link'];

    const TYPE_POST = 'post';
    const TYPE_PAGE = 'page';
    const TURN_ON = 1;

    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISH = 'publish';

    const COMMENT_OPEN = 'open';
    const COMMENT_CLOSE = 'close';

    protected $fillable = [
        'id_posts',
        'post_title',
        'post_slug',
        'post_content',
        'post_excerpt',
        'post_status',
        'comment_status',
        'publish_date',
        'created_by',
        'modified_by',
    ];

    protected $dates = [
        'publish_date',
    ];

    public function getLinkAttribute()
    {
        $exp = explode('-', $this->getOriginal('publish_date') ?? "");
        $year = (int) ($exp[0] ?? 0);
        $month = (int) ($exp[1] ?? 0);
        $date = (int) ($exp[2] ?? 0);
        $slug = (string) ($this->getOriginal('post_slug'));

        return sprintf('%d/%d/%d/%s', $year, $month, $date, $slug);
    }

    public function meta()
    {
        return $this->hasMany(PostMeta::class, 'post_id');
    }

    public function cover()
    {
        return $this->meta()->where('meta_key', PostMeta::KEY_COVER_IMAGE)->take(1);
    }

    public function topic()
    {
        return $this->belongsToMany(PostTermTaxo::class, 'term_relationships', 'object_id', 'term_taxonomy_id');
    }
}
