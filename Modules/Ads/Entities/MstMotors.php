<?php

namespace Modules\Ads\Entities;

use Illuminate\Database\Eloquent\Model;

class MstMotors extends Model
{
    protected $fillable = [];
    protected $table = 'mst_motors';

    public static function getTableName()
    {
        return with(new Static)->getTable();
    }

    public static function getPrimaryKey()
    {
        return with(new Static)->getKeyName();
    }
}
