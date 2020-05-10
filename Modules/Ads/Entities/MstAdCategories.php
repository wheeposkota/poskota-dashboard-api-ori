<?php

namespace Modules\Ads\Entities;

use Illuminate\Database\Eloquent\Model;

class MstAdCategories extends Model
{
    protected $fillable = [];
    protected $table = 'mst_ad_categories';

    const TAXONOMY_BRAND = 'brand';
    const HOME_PROVINCES = ['DKI JAKARTA', 'JAWA BARAT' , 'BANTEN'];
    const SUB_CATEGORY = ['mobil-dijual', 'motor-dijual'];
    const SUB_LOCATION = ['rumah-dijual', 'rumah-dicari', 'rumah-dikontrakan'];

    public function parent()
    {
        return $this->belongsTo(MstAdCategories::class, 'mst_parent_id');
    }

    public function childrens()
    {
        return $this->hasMany(MstAdCategories::class, 'mst_parent_id');
    }

    public function allParents()
    {
        return $this->parent()->with('allParents');
    }

    public function allChildrens()
    {
        return $this->childrens()->with('allChildrens');
    }

    public function rltAdCategories()
    {
        return $this->hasMany(RltAdsCategories::class, 'category_id');
    }

    public function getLastParentIdAttribute()
    {
        if(empty($this->allParents))
        {
            return $this->getKey();
        }
        else
        {
            return $this->allParents->last_parent_id;
        }
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
