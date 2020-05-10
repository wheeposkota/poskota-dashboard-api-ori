<?php

namespace Modules\Ads\Entities;

use Illuminate\Database\Eloquent\Model;

class AdTaxonomy extends Model
{
    protected $fillable = [];
    protected $table = "ad_taxonomy";

    public function term()
    {
        return $this->belongsTo('\Gdevilbat\SpardaCMS\Modules\Taxonomy\Entities\Terms', 'term_id');
    }

	public function category()
    {
    	return $this->belongsTo(MstAdCategories::class, 'category_id');
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
