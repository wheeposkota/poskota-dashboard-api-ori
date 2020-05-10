<?php

namespace Modules\Ads\Entities;

use Illuminate\Database\Eloquent\Model;

class TrxAdsWeb extends Model
{
    protected $fillable = [];
    protected $table = 'trx_ads_web';
    public $timestamps = false;

    public function getFilePathAttribute()
    {
        $file = explode('/', $this->path_ads);

        return end($file);
    }

    public function author()
    {
        return $this->belongsTo('\Gdevilbat\SpardaCMS\Modules\Core\Entities\User', 'created_by');
    }

    public function trxOrder()
    {
        return $this->belongsTo(\App\Models\TrxOrder::class, 'order_id');
    }

    public function mstAdPosition()
    {
        return $this->belongsTo(MstAdsPosition::class, 'mst_ads_position_id');
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
