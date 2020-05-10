<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommodityUpdate extends Model
{
    use SoftDeletes;

    const COMM_UP = 'naik';
    const COMM_DOWN = 'turun';
    const COMM_STABLE = 'tetap';

    protected $fillable = [
        'type_id',
        'member_id',
        'content',
        'price',
        'log',
        'price_before',
    ];

    public function type()
    {
        return $this->belongsTo(CommodityType::class, 'type_id');
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
