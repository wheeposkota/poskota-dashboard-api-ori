<?php

namespace Modules\Ads\Entities;

use Illuminate\Database\Eloquent\Model;

class RltTaxonomyAds extends Model
{
    protected $fillable = [];
    protected $table = 'taxonomy_relationship_ads';

    public function adTaxonomy()
    {
    	return $this->belongsTo(AdTaxonomy::class, 'ad_taxonomy_id');
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
