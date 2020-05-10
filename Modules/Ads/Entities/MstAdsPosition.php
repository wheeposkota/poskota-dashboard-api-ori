<?php

namespace Modules\Ads\Entities;

use Illuminate\Database\Eloquent\Model;

class MstAdsPosition extends Model
{
    protected $fillable = [];
    protected $table = 'mst_ads_position';

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
