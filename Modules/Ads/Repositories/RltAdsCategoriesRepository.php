<?php

namespace Modules\Ads\Repositories;

use Gdevilbat\SpardaCMS\Modules\Core\Repositories;
use Gdevilbat\SpardaCMS\Modules\Core\Repositories\AbstractRepository;

use Modules\Ads\Entities\MstAds as MstAds_m;
use Modules\Ads\Entities\MstAdCategories as MstAdCategories_m;
use Modules\Ads\Entities\RltAdsCategories as RltAdsCategories_m;
use Auth;

/**
 * Class EloquentCoreRepository
 *
 * @package Gdevilbat\SpardaCMS\Modules\Core\Repositories\Eloquent
 */
class RltAdsCategoriesRepository extends AbstractRepository
{
   /**
     * Build Query to catch resources by an array of attributes and params
     * @param  array $attributes
     * @param  null|string $orderBy
     * @param  string $sortOrder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function buildQueryByCreatedUser(array $attributes, $orderBy = null, $sortOrder = 'asc')
    {
        $query = parent::buildQueryByCreatedUser($attributes, $orderBy, $sortOrder);

        return $this->getRltAdCategories($query);
    }

    private function getRltAdCategories($model)
    {
        return $model->leftJoin(MstAds_m::getTableName(), MstAds_m::getTableName().'.'.MstAds_m::getPrimaryKey(), '=', RltAdsCategories_m::getTableName().'.ads_id')
                        ->leftJoin(MstAdCategories_m::getTableName(), MstAdCategories_m::getTableName().'.'.MstAdCategories_m::getPrimaryKey(), '=', RltAdsCategories_m::getTableName().'.category_id')
                       ->select(RltAdsCategories_m::getTableName().'.*', MstAds_m::getTableName().'.mst_ad_name', MstAdCategories_m::getTableName().'.mst_ad_cat_name');
    }
}
