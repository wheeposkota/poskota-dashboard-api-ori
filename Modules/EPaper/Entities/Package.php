<?php

namespace Modules\EPaper\Entities;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [];
    protected $table = 'epaper_package';

    public function author()
    {
        return $this->belongsTo('\Gdevilbat\SpardaCMS\Modules\Core\Entities\User', 'created_by');
    }

    public function setPackagePriceAttribute($value)
    {
        $this->attributes['package_price'] = preg_replace('/[,_]/', '',$value);
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
