<?php

namespace Modules\Commodity\Repositories;

use Gdevilbat\SpardaCMS\Modules\Core\Repositories;
use Gdevilbat\SpardaCMS\Modules\Core\Repositories\AbstractRepository;

use App\Models\CommodityType as CommodityType_m;
use App\Models\CommodityUpdate as CommodityUpdate_m;
use App\Models\Member as Member_m;

use Auth;

/**
 * Class EloquentCoreRepository
 *
 * @package Gdevilbat\SpardaCMS\Modules\Core\Repositories\Eloquent
 */
class CommodityUpdateRepository extends AbstractRepository
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

        return $this->getCommodityCategories($query);
    }

    private function getCommodityCategories($model)
    {
        return $model->leftJoin(CommodityType_m::getTableName().' as category', 'category.'.CommodityType_m::getPrimaryKey(), '=', CommodityUpdate_m::getTableName().'.type_id')
                    ->leftJoin(Member_m::getTableName().' as author', 'author.'.Member_m::getPrimaryKey(), '=', CommodityUpdate_m::getTableName().'.member_id')
                        ->select(CommodityUpdate_m::getTableName().'.*', 'category.type as type', 'author.name as author');
    }
}
