<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;

class Repository {
    const MODULE_POST_LIST = 'post_list';
    const MODULE_POST_RELATED = 'post_related';
    const MODULE_POST_DETAIL = 'post_detail';
    const MODULE_MEDIA_DETAIL = 'media_detail';
    const MODULE_CATEGORY = 'category';
    const MODULE_MEDIA_LIST = 'media_list';
    const MODULE_COMMENT_LIST = 'comment_list';
    const MODULE_EPAPER_LIST = 'epaper_list';
    const MODULE_EPAPER_DETAIL = 'epaper_detail';
    const MODULE_OTHER_PAGE = 'other_page';
    const MODULE_OTHER_PAGE_DETAIL = 'other_page_detail';
    const MODULE_COMMODITY = 'commodity';
    const MODULE_COMMODITY_TYPE = 'commodity_type';
    const MODULE_AD_CATEGORY = 'ad_category';
    const MODULE_AD_CLASSIC = 'ad_classic';
    const MODULE_AD_BANNER = 'ad_banner';

    protected function queryCount(Builder $query)
    {
        return $query->count();
    }

    protected function queryRows(Builder $query, int $page, int $limit)
    {
        $index = $page - 1;
        if ($index < 0)
            $index = 0;

        return $query->skip($limit * $index)->take($limit)->get();
    }

    protected function queryRow(Builder $query)
    {
        return $query->first();
    }

    protected function keyBuilder($module, array $param)
    {
        $key = $module;
        foreach ($param as $k => $p)
            $key .= sprintf(":%s-%s", $k, $p);
        return $key;
    }
}
