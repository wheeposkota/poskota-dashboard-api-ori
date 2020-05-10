<?php

namespace App\Models;

use Gdevilbat\SpardaCMS\Modules\Post\Entities\Post;

class PostCMS extends Post
{
	protected $appends = ['link'];

    public function relatedPost()
    {
    	return $this->belongsToMany(PostCMS::class, 'related_post', 'post_id', 'related_post_id');
    }

    public function editor()
    {
        return $this->belongsTo('\Gdevilbat\SpardaCMS\Modules\Core\Entities\User', 'modified_by');
    }

    public function getLinkAttribute()
    {
        $exp = explode('-', $this->getOriginal('publish_date') ?? "");
        $year = (int) ($exp[0] ?? 0);
        $month = (int) ($exp[1] ?? 0);
        $date = (int) ($exp[2] ?? 0);
        $slug = (string) ($this->getOriginal('post_slug'));

        return sprintf('%d/%d/%d/%s', $year, $month, $date, $slug);
    }
}
