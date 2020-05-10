<?php

namespace Modules\Ads\Entities;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class TrxAdsClassic extends Model
{
    use SoftDeletes;
    
    protected $fillable = [];
    protected $table = 'trx_ads_classic';
    public $timestamps = false;

    public function author()
    {
        return $this->belongsTo('\Gdevilbat\SpardaCMS\Modules\Core\Entities\User', 'created_by');
    }

    public function schedules()
    {
        return $this->hasMany(ClassicAdsSchedule::class, 'trx_ad_classic_id');
    }

    public function trxAdClassic()
    {
        return $this->belongsTo(SELF::class, SELF::getPrimaryKey());
    }

    public function rltAdCategory()
    {
        return $this->belongsTo(RltAdsCategories::class, 'rlt_trx_ads_category_id');
    }

    public function rltTaxonomyAds()
    {
        return $this->hasOne(RltTaxonomyAds::class, 'object_id');
    }

    public function city()
    {
        return $this->belongsTo(MstCities::class, 'mst_city_id');
    }

    public function mstAdType()
    {
        return $this->belongsTo(MstAdTypes::class, 'mst_ad_type_id');
    }

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
