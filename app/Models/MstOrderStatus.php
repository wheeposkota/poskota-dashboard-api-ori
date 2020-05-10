<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstOrderStatus extends Model
{
    protected $table = 'mst_order_status';

    protected $fillable = [
        'status_name'
    ];

    public static function getTableName()
    {
        return with(new Static)->getTable();
    }

    public static function getPrimaryKey()
    {
        return with(new Static)->getKeyName();
    }
}
