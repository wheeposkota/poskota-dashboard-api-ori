<?php

namespace Modules\Gallery\Entities;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = [];
    protected $table = 'gallery';
    protected $primaryKey = 'id_gallery';
    protected $casts = [
        'gallery_source' => 'array',
    ];

    public function author()
    {
        return $this->belongsTo('\Gdevilbat\SpardaCMS\Modules\Core\Entities\User', 'created_by');
    }

    /**
     * Set the Post Status.
     *
     * @param  string  $value
     * @return void
     */
    public function setGalleryStatusAttribute($value)
    {
        if(!empty($value))
        {
            $this->attributes['gallery_status'] = 'publish';
        }
        else
        {
            $this->attributes['gallery_status'] = 'draft';
        }
    }

    public function getGalleryStatusBoolAttribute()
    {
        if($this->post_status == 'publish')
            return true;

        return false;
    }

    public static function getTableName()
    {
        return with(new Static)->getTable();
    }

    public static function getPrimaryKey()
    {
        return with(new Static)->getKeyName();
    }
}
