<?php

namespace Modules\Ads\Entities;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class ClassicAdsSchedule extends Model
{
    use SoftDeletes;

    protected $fillable = [];
    protected $table = 'classic_ads_schedule';
    public $timestamps = false;

    public function trxOrder()
    {
        return $this->belongsTo(\App\Models\TrxOrder::class, 'order_id');
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
