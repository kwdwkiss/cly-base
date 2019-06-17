<?php


namespace Cly\Swoole\Pool;


use Swoole\Coroutine\Channel;

/**
 * Class BasePool
 * @package Cly\Swoole\Pool
 *
 * @method push($client)
 * @method pop()
 * @method int length()
 */
abstract class BasePool
{
    protected $channel;

    protected $config;

    public function __construct($size = 10, $config = [])
    {
        $this->channel = new Channel($size);
        $this->config = $config;
    }

    abstract public function fillFull();

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->channel, $name], $arguments);
    }
}
