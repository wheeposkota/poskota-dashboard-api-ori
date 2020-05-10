<?php

namespace Modules\EPaper\Entities;

use Illuminate\Database\Eloquent\Model;

class EPaper extends Model
{
    protected $fillable = [];
    protected $table = 'electronic_paper';

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
    public function setEpaperStatusAttribute($value)
    {
        if(!empty($value))
        {
            $this->attributes['epaper_status'] = 'publish';
        }
        else
        {
            $this->attributes['epaper_status'] = 'draft';
        }
    }

    public function getEpaperStatusBoolAttribute()
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
