<?php

namespace Modules\Ads\Entities;

use Illuminate\Database\Eloquent\Model;

class MstAds extends Model
{
    protected $fillable = [];
    protected $table = 'mst_ads';

    public function categories()
    {
        return $this->belongsToMany('\Modules\Ads\Entities\MstAdCategories', 'rlt_ads_categories', 'ads_id', 'category_id')
                    ->withPivot([
                            RltAdsCategories::getPrimaryKey(),
                            'min_line',
                            'max_line',
                            'char_on_line',
                            'price'
                    ]);        
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
