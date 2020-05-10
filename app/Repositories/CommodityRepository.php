<?php

namespace App\Repositories;

use App\Models\CommodityType;
use App\Models\CommodityUpdate;
use App\Services\Cache;
use App\Services\Generic;
use Illuminate\Support\Facades\DB;

class CommodityRepository extends Repository
{
    public $commodity;

    public function __construct(CommodityUpdate $commodity)
    {
        $this->commodity = $commodity;
    }

    public function retrieveList($param)
    {
        $keyCache = $this->keyBuilder(self::MODULE_COMMODITY, $param);
        if (Cache::has($keyCache))
            return Cache::get($keyCache);

        $select = [
            'commodity_updates.id',
            'type_id',
            'content',
            'price',
            DB::raw('commodity_types.type as type_name'),
            'commodity_updates.created_at',
        ];

        $query = $this->commodity
            ->select($select)
            ->latest()
            ->where('member_id', $param['id_member'])
            ->leftJoin('commodity_types', 'commodity_updates.type_id', '=', 'commodity_types.id');

        $count = $this->queryCount($query);
        $result = $this->queryRows($query, $param['page'], $param['limit']);

        $result = Generic::paginateQuery($param, $count, $result);

        Cache::put($keyCache, $result);

        return $result;
    }

    public function retrieveListPublished($param)
    {
        $keyCache = $this->keyBuilder(self::MODULE_COMMODITY, $param);
        if (Cache::has($keyCache))
            return Cache::get($keyCache);

        $select = [
            'commodity_updates.id',
            'type_id',
            'content',
            'price',
            'log',
            'price_before',
            DB::raw('commodity_types.type as type_name'),
            'commodity_updates.created_at',
        ];

        $query = $this->commodity
            ->select($select)
            ->latest()
            ->leftJoin('commodity_types', 'commodity_updates.type_id', '=', 'commodity_types.id');

        $count = $this->queryCount($query);
        $result = $this->queryRows($query, $param['page'], $param['limit']);

        $result = Generic::paginateQuery($param, $count, $result);

        Cache::put($keyCache, $result);

        return $result;
    }

    public function retrieveType($param = [])
    {
        $keyCache = $this->keyBuilder(self::MODULE_COMMODITY_TYPE, $param);
        if (Cache::has($keyCache))
            return Cache::get($keyCache);

        $select = [
            'id',
            'type',
        ];

        $result = CommodityType::query()
            ->select($select)
            ->latest()
            ->get();

        Cache::put($keyCache, $result);

        return $result;
    }

    public function find($id)
    {
        return CommodityUpdate::findOrFail($id);
    }

    public function compareBefore($type_id, $price, $id = 0)
    {
        $before = CommodityUpdate::where('type_id', $type_id);
        if ($id != 0)
            $before = $before->where('id', '<', $id);

        $status = CommodityUpdate::COMM_STABLE;

        $before = $before->latest()->first();
        if (is_null($before))
            return [
                $status,
                0
            ];

        if ($price > $before->price)
            $status = CommodityUpdate::COMM_UP;
        if ($price < $before->price)
            $status = CommodityUpdate::COMM_DOWN;

        return [
            $status,
            $before->price
        ];
    }

    public function store($param)
    {
        CommodityUpdate::create($param);
    }

    public function update(CommodityUpdate $commodity, $param)
    {
        $commodity->fill($param)->save();
    }

    public function delete(CommodityUpdate $commodity)
    {
        $commodity->delete();
    }

}
