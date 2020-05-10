<?php

namespace App\Repositories;

use App\Models\AdCategory;
use App\Models\AdPosition;
use App\Models\AdRelationship;
use App\Models\AdTrx;
use App\Models\AdWeb;
use App\Services\Payment;
use Carbon\Carbon;
use App\Services\Cache;
use App\Services\Generic;
use Illuminate\Support\Facades\DB;

class AdRepository extends Repository
{
    public function retrieveCategory($param = [])
    {
        $keyCache = $this->keyBuilder(self::MODULE_AD_CATEGORY, $param);
        if (Cache::has($keyCache))
            return Cache::get($keyCache);

        $select = [
            'id',
            DB::raw('mst_parent_id as parent_id'),
            DB::raw('mst_ad_cat_name as name'),
            DB::raw('mst_ad_cat_slug as slug'),
        ];

        $parent = AdCategory::query()
            ->select($select)
            ->where('mst_parent_id', 0)
            ->get();

        $childs = AdCategory::query()
            ->select($select)
            ->whereIn('mst_parent_id', $parent->pluck('id'))
            ->get();

        $result = $parent->map(function ($item) use ($childs) {
            $subcategory = $childs->where('parent_id', $item->id)->map(function ($item2) {
                return [
                    'name' => $item2->name,
                    'slug' => $item2->slug,
                ];
            });

            return [
                'name' => $item->name,
                'slug' => $item->slug,
                'subcategory' => $subcategory,
            ];
        });

        Cache::put($keyCache, $result);

        return $result;
    }

    public function retrieveAdToday($param)
    {
        $keyCache = $this->keyBuilder(self::MODULE_AD_CLASSIC, $param);
        if (Cache::has($keyCache))
            return Cache::get($keyCache);

        $select = [
            DB::raw('trx_ads_classic.trx_ad_content as content'),
            DB::raw('category.mst_ad_cat_name as category'),
            DB::raw('subcategory.mst_ad_cat_name as subcategory'),
            DB::raw('category.mst_ad_cat_slug as category_slug'),
            DB::raw('subcategory.mst_ad_cat_slug as subcategory_slug'),
            DB::raw('DATE(trx_ad_publish_date) as publish_date'),
        ];

        $today = Carbon::now()->subHours(9)->format('Y-m-d');

        $query = AdTrx::query()->select($select);

        if ($param['slug']) {
            $slug = AdCategory::select('id', 'mst_parent_id')->where('mst_ad_cat_slug', $param['slug'])->first();
            if ($slug) {
                if ($slug->mst_parent_id == 0) {
                    $ids = AdCategory::select('id')->where('mst_parent_id', $slug->getKey())->get()->pluck('id')->toArray();
                }
                if ($slug->mst_parent_id != 0) {
                    $ids = [ $slug->getKey() ];
                }
                if (count($ids)) {
                    $ids_rlt = AdRelationship::select('id_rlt_ads_categories')->whereIn('category_id', $ids)->get()->pluck('id_rlt_ads_categories')->toArray();
                    $query = $query->whereIn('rlt_trx_ads_category_id', $ids_rlt);
                }
            }
        }

        if ($param['search']) {
            $query = $query->where('trx_ads_classic.trx_ad_content', 'like', '%' . $param['search'] . '%');
        }

        $query = $query
            ->leftJoin('trx_orders', 'trx_ads_classic.order_id', '=', 'trx_orders.id')
            ->leftJoin('classic_ads_schedule', 'trx_ads_classic.id', '=', 'classic_ads_schedule.trx_ad_classic_id')
            ->leftJoin(DB::raw('rlt_ads_categories as relation'), 'trx_ads_classic.rlt_trx_ads_category_id', '=', 'relation.id_rlt_ads_categories')
            ->leftJoin(DB::raw('mst_ad_categories as subcategory'), 'relation.category_id', '=', 'subcategory.id')
            ->leftJoin(DB::raw('mst_ad_categories as category'), 'subcategory.mst_parent_id', '=', 'category.id')
            ->whereDate('classic_ads_schedule.trx_ad_publish_date', $today)
            ->where('trx_orders.order_status_id', Payment::getStatus(Payment::TRX_EXPIRE))
            ->orderBy('subcategory', 'asc')
            ->orderBy('content', 'asc');

        $count = $this->queryCount($query);
        $result = $this->queryRows($query, $param['page'], $param['limit']);

        $result = Generic::paginateQuery($param, $count, $result);
        $result['list_data'] = $result['list_data']->map(function($item) {
            $item->content = trim(preg_replace('/\s\s+/', ' ', $item->content));
            $exp = preg_split('/[\s,]+/', $item->content, 3);
            $item->content_modified = [
                'title' => strtoupper(sprintf('%s %s', $exp[0] ?? "", $exp[1] ?? "")),
                'subtitle' => $exp[2] ?? "",
            ];
            return $item;
        });

        Cache::put($keyCache, $result);

        return $result;
    }

    public function retrieveAdBanner($param)
    {
        $keyCache = $this->keyBuilder(self::MODULE_AD_BANNER, $param);
        if (Cache::has($keyCache))
            return Cache::get($keyCache);

        $select = [
            DB::raw('trx_ads_web.path_ads as image'),
            DB::raw('trx_ads_web.ads_action as action'),
            DB::raw('trx_ads_web.ads_action as title'),
            DB::raw('mst_ads_position.ads_position as position'),
        ];

        $today = Carbon::now()->format('Y-m-d');
        $session_close = Payment::getStatus(Payment::TRX_EXPIRE);

        $query = AdWeb::query()
            ->select($select)
            ->leftJoin('trx_orders', function ($q) {
                $q->on('trx_ads_web.order_id', '=', 'trx_orders.id');
            })
            ->leftJoin('mst_ads_position', function ($q) {
                $q->on('trx_ads_web.mst_ads_position_id', '=', 'mst_ads_position.id');
            })
           ->where('trx_ads_web.trx_ad_publish_date', '<=', $today)
           ->where('trx_ads_web.trx_ad_end_date', '>=', $today)
           ->where('trx_orders.order_status_id', $session_close)
           ->latest(\App\Models\TrxOrder::getTableName().'.created_at');

        if ($param['position'] != 'all') {
            $position = AdPosition::where('ads_position', $param['position'])->first();
            if (! $position)
                return null;

            $query = $query->where('mst_ads_position_id', $position->getKey());
        }

        if ($param['position'] != 'all') {
            $result = $query->first();
            if (is_null($result))
                return null;
            $result->image = generate_cdn($result->image);
        } else { //all
            $result = $query->get()->map(function($i) {
                $i->image = generate_cdn($i->image);
                return $i;
            });
        }

        Cache::put($keyCache, $result);

        return $result;
    }
}
