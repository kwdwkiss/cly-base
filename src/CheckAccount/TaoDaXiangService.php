<?php


namespace Cly\CheckAccount;


use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;

class TaoDaXiangService
{
    protected $cookiePath = '';

    /**
     * @var FileCookieJar
     */
    protected $cookie;

    public function __construct()
    {
        $this->cookie = new FileCookieJar(storage_path('taodaxiang/cookie'), true);
    }

    public function getCreditOrStoreImage($ww)
    {
        $result = $this->getCredit($ww);
        if ($result['code'] == 1) {
            $image = $this->getCaptcha();
            file_put_contents(storage_path('taodaxiang/captcha.png'), $image);
            return null;
        }

        return $result;
    }

    public function getCredit($ww)
    {
        $client = new Client(['cookies' => $this->cookie]);

        $url = 'https://taodaxiang.com/credit2/index/get';

        $response = $client->post($url, [
            'form_params' => [
                'account' => $ww
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getCaptcha()
    {
        $url = 'https://taodaxiang.com/call.php?op=checkcode&code_len=4&font_size=14&width=200&height=80&font_color=&background=';

        $client = new Client(['cookies' => $this->cookie]);

        $response = $client->get($url);

        return $response->getBody()->getContents();
    }

    public function check($code)
    {
        $url = 'https://taodaxiang.com/credit2/index/check';

        $client = new Client(['cookies' => $this->cookie]);

        $response = $client->post($url, [
            'form_params' => [
                'code' => $code
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}