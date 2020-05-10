<?php

namespace App\Services;

use DateTime;
use Exception;
use DateTimeZone;

class Generic {
    public static function paginateQuery($param, $data_count, $data_list)
    {
        $total_page = ceil($data_count / $param['limit']);
        $current_page = $param['page'];
       if ($current_page < 1)
           $current_page = 1;

        if ($current_page < $total_page)
            $next_page = $current_page + 1;
        if ($current_page >= $total_page)
            $next_page = 0;

        if (($current_page-1) < 0)
            $prev_page = 0;
        else $prev_page = $current_page - 1;

        $paginate = [
            'list_data' => $data_list,
            'total_page' => $total_page,
            'next_page' => $next_page,
            'prev_page' => $prev_page,
            'current_page' => $current_page,
            'count_data' => $data_list->count(),
            'total_data' => $data_count,
            'request' => $param,
        ];

        return $paginate;
    }

    public static function getver()
    {
        try {
            $commitHash = trim(exec('git log --pretty="%h" -n1 HEAD'));

            $commitDate = new DateTime(trim(exec('git log -n1 --pretty=%ci HEAD')));
            $commitDate->setTimezone(new DateTimeZone('Asia/Jakarta'));

            $app = config('app.name');
            $env = config('app.env');

            return sprintf('%s [%s] ver.%s (%s)', $app, $env, $commitHash, $commitDate->format('Y-m-d H:i:s'));
        } catch (Exception $e) {
            return 'Ver-err:'.$e->getMessage();
        }
    }
}
