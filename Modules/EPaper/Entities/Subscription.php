<?php

namespace Modules\EPaper\Entities;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [];
    protected $table = 'epaper_subscription';
    public $timestamps = false;

    public function trxOrder()
    {
        return $this->belongsTo(\App\Models\TrxOrder::class, 'order_id');
    }

    public function member()
    {
        return $this->belongsTo(\App\Models\Member::class, 'order_id');
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
