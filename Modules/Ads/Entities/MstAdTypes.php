<?php

namespace Modules\Ads\Entities;

use Illuminate\Database\Eloquent\Model;

class MstAdTypes extends Model
{
    protected $fillable = [];
    protected $table = 'mst_ad_types';

    public static function getTableName()
    {
        return with(new Static)->getTable();
    }

    public static function getPrimaryKey()
    {
        return with(new Static)->getKeyName();
    }
}
