<?php

namespace Modules\Ads\Entities;

use Illuminate\Database\Eloquent\Model;

class MstCities extends Model
{
    protected $fillable = [];
    protected $table = 'mst_cities';

    public function province()
    {
    	return $this->belongsTo(MstProvinces::class, 'mst_province_id');
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
