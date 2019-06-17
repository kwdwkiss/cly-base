<?php

namespace Cly\Tool;

use ipip\db\City;
use Symfony\Component\DomCrawler\Crawler;

class IpService
{
    public function getForCache($ip, $minutes = 60 * 24 * 7)
    {
        $key = 'ip:' . $ip;

        return \Cache::remember($key, $minutes, function () use ($ip) {
            return $this->getIpipDb($ip);
        });
    }

    public function getIpipDb($ip)
    {
        $city = new City(storage_path('ip/ipipfree.ipdb'));
        $data = $city->find($ip, 'CN');
        return array_get($data, 1) . ' ' . array_get($data, 2);
    }

    public function getBaidu($ip)
    {
        $url = "https://sp0.baidu.com/8aQDcjqpAAV3otqbppnN2DJv/api.php?query={$ip}&co=&resource_id=6006&t=1559098021493&ie=utf8&oe=gbk&cb=op_aladdin_callback&format=json&tn=baidu&cb=jQuery110207189068598110522_1559097985141&_=1559097985143";

        $data = file_get_contents($url);

        $data = iconv('gbk', 'utf-8', $data);

        preg_match('/.*jQuery[\d_]+\((.*)\);/', $data, $matches);

        $data = json_decode($matches[1], true) ?: [];

        return array_get($data, 'data.0.location');
    }

    public function getCz88($ip)
    {
        $url = 'http://ip.cz88.net/data.php?ip=';

        $data = file_get_contents($url . $ip);
        $data = iconv('gbk', 'utf8', $data);

        //$demoStr = "ShowIPAddr('171.106.17.32','广西桂林市 电信','未知操作系统 未知浏览器 ');";

        preg_match('/ShowIPAddr\((.*)\);/', $data, $matches);
        $match = $matches[1];

        return trim(explode(',', $match)[1], '\'');
    }

    /**
     * Support ipv6
     */
    public function getZxinc($ip)
    {
        $url = 'http://ip.zxinc.org/ipquery/?ip=';

        $data = file_get_contents($url . $ip);

        $crawler = new Crawler($data);

        $dom = $crawler->filter('td:contains(地理位置)+td')->eq(0);

        return $dom->text();
    }
}
