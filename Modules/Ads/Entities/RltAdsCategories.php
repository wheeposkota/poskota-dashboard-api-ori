<?php

namespace Modules\Ads\Entities;

use Illuminate\Database\Eloquent\Model;

class RltAdsCategories extends Model
{
    protected $fillable = [];
    protected $table = 'rlt_ads_categories';
    protected $primaryKey = 'id_rlt_ads_categories';

    public function mstAd()
    {
    	return $this->belongsTo(MstAds::class, 'ads_id');
    }

    public function mstAdsCategory()
    {
    	return $this->belongsTo(MstAdCategories::class, 'category_id');
    }

    public function trxAdClassics()
    {
        return $this->hasMany(TrxAdsClassic::class, 'rlt_trx_ads_category_id');
    }

    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = preg_replace('/[,_]/', '',$value);
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
