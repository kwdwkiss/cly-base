<?php


namespace Cly\Swoole\Pool;


use Swoole\Coroutine\Redis;

/**
 * Class RedisPool
 * @package Cly\Swoole\Pool
 *
 * @method push(Redis $client)
 * @method Redis pop()
 */
class RedisPool extends BasePool
{

    public function fillFull()
    {
        while (!$this->channel->isFull()) {
            $client = new Redis();

            $ip = $this->config['ip'] ?? '127.0.0.1';
            $port = $this->config['port'] ?? '6379';

            $res = $client->connect($ip, $port);
            if ($res == false) {
                throw new \RuntimeException("failed to connect server.");
            } else {
                $this->push($client);
                echo "channel-redis-length:" . $this->length() . "\n";
            }
        }
    }
}
