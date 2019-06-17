<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/6/9
 * Time: 下午10:19
 */

use Symfony\Component\DomCrawler\Crawler;

if (!function_exists('get_client_ip')) {
    function get_client_ip()
    {
        return request()->getClientIp();
    }
}

if (!function_exists('get_geo')) {
    function get_geo($ip)
    {
        return app(\Cly\Tool\IpService::class)->getForCache($ip);
    }
}

if (!function_exists('get_geo_db')) {
    function get_geo_db($ip)
    {
        return app(\Cly\Tool\IpService::class)->getIpipDb($ip);
    }
}

if (!function_exists('is_wechat_agent')) {
    function is_wechat_agent()
    {
        return strpos(request()->userAgent(), 'MicroMessenger') !== false;
    }
}

if (!function_exists('need_wechat_oauth')) {
    function need_wechat_oauth()
    {
        if (app('wechat.official_account.default') && is_wechat_agent() && env('WECHAT_OAUTH_ENABLE')) {
            return session('wechat.oauth_user.default') ? false : true;
        }
        return false;
    }
}

if (!function_exists('cache_get_remember')) {
    function cache_get_remember($key, $callback, $ttl = 1)
    {
        if ($value = Cache::get($key)) {
            return json_decode($value, true);
        }
        $value = value($callback);
        Cache::set($key, json_encode($value), $ttl);
        return $value;
    }
}
