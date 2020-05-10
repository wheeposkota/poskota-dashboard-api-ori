<?php

namespace App\Repositories;

use App\Models\TrxOrder;
use App\Models\TrxOrderMeta;
use App\Models\EPaper;
use App\Models\EPaperPackage;
use App\Models\EPaperSubscription;
use App\Models\Media;
use App\Models\Member;
use App\Services\Generic;
use App\Services\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EpaperRepository extends Repository
{
    public $epaper;

    public function __construct(EPaper $epaper)
    {
        $this->epaper = $epaper;
    }

    public function retrieveEPaperList($param)
    {
        $keyCache = $this->keyBuilder(self::MODULE_EPAPER_LIST, $param);
        if (Cache::has($keyCache))
            return Cache::get($keyCache);

        $select = [
            'epaper_title',
            'epaper_edition',
            'epaper_file',
        ];

        $query = $this->epaper
            ->select($select)
            ->where('epaper_status', EPaper::STATUS_PUBLISH)
            ->orderBy('epaper_edition', 'desc');

        $count = $this->queryCount($query);
        $result = $this->queryRows($query, $param['page'], $param['limit']);

        $result = Generic::paginateQuery($param, $count, $result);

        Cache::put($keyCache, $result);

        return $result;
    }

    public function retrieveEpaperDetail($param)
    {
        $keyCache = $this->keyBuilder(self::MODULE_EPAPER_DETAIL, $param);
        if (Cache::has($keyCache))
            return Cache::get($keyCache);

        $select = [
            'epaper_title',
            'epaper_edition',
            'epaper_file',
        ];

        $result = $this->epaper
            ->select($select)
            ->where('epaper_status', Media::STATUS_PUBLISH)
            ->where('epaper_edition', $param['date'])
            ->firstOrFail();

        Cache::put($keyCache, $result);

        return $result;
    }

    public function retrieveEPaperPackage()
    {
        $select = [
            'id',
            'package_name',
            'package_price',
            'package_period',
            'package_description',
        ];

        $query = EPaperPackage::query()
            ->select($select)
            ->orderBy('package_period', 'asc')
            ->get();

        return $query;
    }

    public function retrieveEPaperPackageDetail($id)
    {
        $select = [
            'id',
            'package_name',
            'package_price',
            'package_period',
            'package_description',
        ];

        $query = EPaperPackage::query()
            ->select($select)
            ->orderBy('package_period', 'asc')
            ->findOrFail($id);

        return $query;
    }

    public function checkEPaperSubs(Member $user)
    {
        $today = Carbon::now()->format('Y-m-d');

        $result = EPaperSubscription::query()
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->where('member_id', $user->getKey())
            ->first();

        if (! is_null($result))
            $result->load('package');

        return $result;
    }

    public function historyEPaperSubs($param, Member $user)
    {
        $query = TrxOrder::query()
            ->select([ 'trx_orders.id' ])
            ->leftJoin('trx_orders_meta', function ($q) {
                $q->on('trx_orders_meta.trx_order_id', '=', 'trx_orders.id')
                    ->where('trx_orders_meta.meta_key', TrxOrderMeta::PAYLOAD_EPAPER);
            })
            ->where(DB::raw('JSON_EXTRACT(trx_orders_meta.meta_value, "$.member_id")'), $user->getKey())
            ->orderBy('trx_orders.created_at');

        $count = $this->queryCount($query);
        $result = $this->queryRows($query, $param['page'], $param['limit']);
        $result = TrxOrder::query()
            ->with('meta')
            ->whereIn('id', $result->pluck('id'))
            ->latest()
            ->get();

        $result = Generic::paginateQuery($param, $count, $result);

        return $result;
    }

}
