<?php

namespace Modules\Agent\Entities;

use Illuminate\Database\Eloquent\Model;

class RltMstAgensUsers extends Model
{
    protected $fillable = [];
    protected $table = 'rlt_mst_agens_users';

    public static function getTableName()
    {
        return with(new Static)->getTable();
    }

    public static function getPrimaryKey()
    {
        return with(new Static)->getKeyName();
    }
}
