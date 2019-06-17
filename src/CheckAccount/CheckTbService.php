<?php

namespace Cly\CheckAccount;

use GuzzleHttp\Client;
use Modules\Common\Entities\CheckTbCredit;
use mysql_xdevapi\Exception;

class CheckTbService
{
    public function getCreditFromThird($ww)
    {
        $client = new Client();

        $uri = '/web/wangwang_info?client=50&format=json&timestamp=${timestamp}&username=${username}&ver=4';
        $uri = str_replace(['${timestamp}', '${username}'], [time(), $ww], $uri);

        $sign = md5(urlencode($uri));

        $url = 'http://www.kehuda.com' . $uri . "&sign={$sign}";

        $response = $client->get($url);

        if ($response->getStatusCode() != 200) {
            throw new \Exception($response->getStatusCode());
        }

        return $response->getBody()->getContents();
    }

    public function getCredit($ww, $expireDay = 7)
    {
        if (empty($ww)) {
            throw new Exception('旺旺号不能为空');
        }

        $query = CheckTbCredit::query();
        $query->where('ww', $ww);
        if (is_numeric($expireDay)) {
            $query->where('created_at', '>', now()->subDay($expireDay));
        }

        $checkTbCredit = $query->first();

        if ($checkTbCredit) {
            return $checkTbCredit;
        }

        $data = $this->getCreditFromThird($ww);
        $data = json_decode($data, true)['kehuda'] ?? [];

        if ($data['status'] ?? null == 1) {
            $checkTbCreditData = $data['data'] ?? [];

            return CheckTbCredit::create(['ww' => $ww] + $checkTbCreditData);
        }

        logger('getCreditFromThird', [$data]);
    }
}