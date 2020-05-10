<?php

namespace Modules\Ads\Entities;

use Illuminate\Database\Eloquent\Model;

class MstCars extends Model
{
    protected $fillable = [];
    protected $table = 'mst_cars';

    public static function getTableName()
    {
        return with(new Static)->getTable();
    }

    public static function getPrimaryKey()
    {
        return with(new Static)->getKeyName();
    }
}
