<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Request;

if (!function_exists('ribuan')) {
    function ribuan($value)
    {
        return number_format($value, 0, '', '.');
    }
}

if (!function_exists('asset_share')) {
    function asset_share($asset)
    {
        return asset('uploads/' . $asset);
    }
}

if (!function_exists('no_pic')) {
    function no_pic($type)
    {
        switch ($type) {
            case 'epaper':
                $file = 'epaper.jpg';
                break;
            case 'user':
                $file = 'nouser.png';
                break;
            case 'news':
            default:
                $file = 'nopic.png';
                break;
        }
	$front_url = env('FRONTEND_URL', 'https://poskota.id');
        return sprintf('%s/%s', $front_url, $file);
    }
}

if (!function_exists('dateid')) {
    function dateid($time) { // "Asia/Tokyo"
        $format = 'l, d F Y - H:i';
        $day   = array('Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min');
        $days  = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu');
        $month = array('', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov' ,'Des');
        $months= array('', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November' ,'Desember');

        if(!is_a($time, 'DateTime')) {
            if(is_int($time)) {
                $time = new DateTime(date('Y-m-d H:i:s.u',$time));
            }elseif(is_string($time)){
                try {
                    $time = new DateTime($time);
                } catch (Exception $e) {
                    $time = new DateTime();
                }
            }else{
                $time = new DateTime();
            }
        }
        $ret = '';
        for($i=0;$i<strlen($format);$i++) {
            switch($format[$i]) {
                case 'D' : $ret .= $day[ $time->format('w') ]; break;
                case 'l' : $ret .= $days[ $time->format('w') ]; break;
                case 'M' : $ret .= $month[ $time->format('n') ]; break;
                case 'F' : $ret .= $months[ $time->format('n') ]; break;
                case '\\': $ret .= $format[ $i+1 ]; $i++; break;
                default  : $ret .= $time->format( $format[$i] ); break;
            }
        }
        return $ret;
    }
}

if (!function_exists('generate_cdn')) {
    function generate_cdn($asset)
    {
        if (is_null($asset))
            return no_pic('news');

//        if (! Storage::exists($asset))
//            return no_pic('news');

        try {
            $link = Storage::temporaryUrl($asset, now()->addMinutes(60 * 24 * 5));
        } catch (\Exception $e) {
            report($e);
            return no_pic('news');
        }
        return $link;
    }
}

if (!function_exists('human_readable_datetime')) {
    function human_readable_datetime($datetime)
    {
        try {
            $date = $datetime->format('l, d F Y - H:i \W\I\B');
        } catch(\Exception $e) {
            report($e);
            $date = $datetime->toDatetimeString();
        }
        return $date;
    }
}

if (!function_exists('get_words')) {
    function get_words($sentence, $count = 10)
    {
        // random just show ""
        preg_match("/(?:\w+(?:\W+|$)){0,$count}/", $sentence, $matches);
        return $matches[0];
    }
}

if (!function_exists('get_excerpt')) {
    function get_excerpt($sentence, $show_dots = true)
    {
        $sentence = strip_tags($sentence);
        $sentence = str_replace("&nbsp;", '', $sentence);
        $excerpt = substr($sentence, 0, 150);
        if ($show_dots)
            $excerpt .= ' [...]';
        return $excerpt;
    }
}

if (!function_exists('make_meta_keyword')) {
    function make_meta_keyword($string)
    {
        preg_match_all("/[a-z0-9\-]{4,}/i", $string, $output_array);

        if(is_array($output_array) && count($output_array[0])) {
            return strtolower(implode(',', $output_array[0]));
        } else {
            return '';
        }
    }
}

if (!function_exists('set_asset_link')) {
    function set_asset_link($text)
    {
        // $text = str_replace("\r\n", '', nl2br($text));
        return $text;
        $text = utf8_encode(html_entity_decode($text));
        $xml = simplexml_load_string("<p>".$text."</p>");
        $list = $xml->xpath("//@href");

        $preparedUrls = array();
        foreach($list as $item) {
            $item = parse_url($item);
            $link_old = sprintf('%s://%s%s', $item['scheme'], $item['host'], $item['path']);
            $link_new = str_replace('https://poskotanews.com/cms/wp-content/uploads/', '', $link_old);
            $link_new = generate_cdn($link_new);

            $preparedUrls[] = [
                'old' => $link_old,
                'new' => $link_new,
            ];
        }

        foreach($preparedUrls as $url) {
            $text = str_replace($url['old'], $url['new'], $text);
        }

        return $text;
    }
}

if (!function_exists('handle_upload_file')) {
    function handle_upload_file($key = 'file', $directory, $prefix = 'file')
    {
        $file = Request::file($key);
        $original = $file->getClientOriginalName();

        if (!$file->isValid())
            throw new \Exception(sprintf("Upload file %s is not valid", $original));

        try {
            $filename = sprintf('%s_%s_%s.%s', date('Y-m-d'), $prefix, md5(microtime(true)), $file->extension());
            $file->move(public_path(sprintf('uploads/%s', $directory)), $filename);
        } catch (Exception $e) {
            report($e);
            $filename = 'nopic.png';
        }

        return [
            'original' => $original,
            'saved' => $filename,
        ];
    }
}
