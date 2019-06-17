<?php


namespace Cly\Swoole\Pool;


use Swoole\Coroutine\MySQL;

/**
 * Class MysqlPool
 * @package Cly\Swoole\Pool
 *
 * @method push(MySQL $client)
 * @method MySQL pop()
 */
class MysqlPool extends BasePool
{

    public function fillFull()
    {
        while (!$this->channel->isFull()) {
            $client = new MySQL();

            $ip = $this->config['ip'] ?? '127.0.0.1';
            $port = $this->config['port'] ?? '3306';
            $user = $this->config['user'] ?? 'root';
            $password = $this->config['password'] ?? '';
            $database = $this->config['database'] ?? '';

            $res = $client->connect([
                'host' => $ip,
                'user' => $user,
                'password' => $password,
                'database' => $database
            ]);
            if ($res == false) {
                throw new \RuntimeException("failed to connect server.");
            } else {
                $this->push($client);
                echo "channel-mysql-length:" . $this->length() . "\n";
            }
        }
    }
}
