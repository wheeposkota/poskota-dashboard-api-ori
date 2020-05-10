<?php

namespace Modules\Agent\Entities;

use Illuminate\Database\Eloquent\Model;

class MstDiscount extends Model
{
    protected $fillable = [];
    protected $table = 'mst_discount';

    public static function getTableName()
    {
        return with(new Static)->getTable();
    }

    public static function getPrimaryKey()
    {
        return with(new Static)->getKeyName();
    }
}
