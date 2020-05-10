<?php

namespace Modules\Ads\Repositories;

use Gdevilbat\SpardaCMS\Modules\Core\Repositories;
use Gdevilbat\SpardaCMS\Modules\Core\Repositories\AbstractRepository;

use Modules\Ads\Entities\MstAdCategories as MstAdCategories_m;

use Auth;

/**
 * Class EloquentCoreRepository
 *
 * @package Gdevilbat\SpardaCMS\Modules\Core\Repositories\Eloquent
 */
class MstAdCategoriesRepository extends AbstractRepository
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

        return $this->getAdCategories($query);
    }

    private function getAdCategories($model)
    {
        return $model->leftJoin(MstAdCategories_m::getTableName().' as parent', 'parent.'.MstAdCategories_m::getPrimaryKey(), '=', MstAdCategories_m::getTableName().'.mst_parent_id')
                        ->select(MstAdCategories_m::getTableName().'.*', 'parent.mst_ad_cat_name as parent_name');
    }
}
