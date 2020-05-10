<?php

namespace Modules\Ads\Entities;

use Illuminate\Database\Eloquent\Model;

class RltAdsMember extends Model
{
    protected $fillable = [];
    protected $table = 'rlt_ads_member';

    public static function getTableName()
    {
        return with(new Static)->getTable();
    }

    public static function getPrimaryKey()
    {
        return with(new Static)->getKeyName();
    }
}
