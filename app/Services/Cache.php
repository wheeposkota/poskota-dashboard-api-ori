<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache as Base;
use App\Repositories\Repository;
use Illuminate\Support\Facades\Redis;

class Cache {
    const TIME = 2; //in minutes

    public static function has($key)
    {
        // return Base::has($key);

        $r = self::instance();
        if (is_null($r)) return false;
        $k = self::keybuilder($key);

        try {
            $exist = (bool) $r->exists($k);
        } catch (Exception $e) {
            report($e);
            return false;
        }

        return $exist;
    }

    public static function put($key, $data)
    {
        // return Base::pust($key, $data, self::TIME);

        $r = self::instance();
        if (is_null($r)) return false;
        $k = self::keybuilder($key);

        try {
            $r->set($k, serialize($data), 'EX', self::TIME * 60);
        } catch (Exception $e) {
            report($e);
            return;
        }
    }

    public static function get($key)
    {
        // return Base::get($key);

        $r = self::instance();
        if (is_null($r)) return false;
        $k = self::keybuilder($key);

        $data = $r->get($k);

        return unserialize($data);
    }

//    public static function regenerateCache($key,$param)
//    {
//        self::instance();
//        switch ($key) {
//            case Repository::MODULE_CATEGORY:
//                break;
//        }
//    }

    private static function instance()
    {
        try {
            $redis = Redis::connection('cache');
        } catch (Exception $e) {
            report($e);
            return null;
        }
        return $redis;
    }

    private static function keybuilder($key)
    {
        $cachekey = sprintf('%s:%s', config('cache.prefix'), $key);
        return $cachekey;
    }
}
