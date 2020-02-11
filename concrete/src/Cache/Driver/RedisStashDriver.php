<?php

/*
 * Modified for prefix By Derek Cameron <info@derekcameron.com>
 * This file is Originally part of the Stash package.
 *
 * (c) Robert Hafner <tedivm@tedivm.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * Can be found in vendor/tedivm/stash
 */
namespace Concrete\Core\Cache\Driver;

use Stash\Driver\AbstractDriver;
use Redis;
use RedisArray;

class RedisStashDriver extends AbstractDriver
{
    /**
     * The Redis drivers.
     *
     * @var \Redis|\RedisArray
     */
    protected $redis;

    protected $prefix;

    /**
     * The cache of indexed keys.
     *
     * @var array
     */
    protected $keyCache = [];

    protected $redisArrayOptionNames = [
        'previous',
        'function',
        'distributor',
        'index',
        'autorehash',
        'pconnect',
        'retry_interval',
        'lazy_connect',
        'connect_timeout',
    ];

    /**
     * The options array should contain an array of servers,.
     *
     * The "server" option expects an array of servers, with each server being represented by an associative array. Each
     * redis config must have either a "socket" or a "server" value, and optional "port" and "ttl" values (with the ttl
     * representing server timeout, not cache expiration).
     *
     * The "database" option lets developers specific which specific database to use.
     *
     * The "password" option is used for clusters which required authentication.
     *
     * @param array $options The `concrete.cache.levels.{level}.drivers.redis.options` config item
     */
    protected function setOptions(array $options = [])
    {
        $options += $this->getDefaultOptions();
        $this->prefix = !empty($options['prefix']) ? $options['prefix'] : null;
        // Normalize Server Options
        $servers = [];
        foreach ($this->getRedisServers(array_get($options, 'servers', [])) as $server) {
            $servers[] = $server;
        }
        // Get Redis Instance
        $redis = $this->getRedisInstance($servers);

        // select database - we use isset because the database can be 0
        if (isset($options['database'])) {
            $redis->select($options['database']);
        }
        if (!empty($options['prefix'])) {
            $redis->setOption(\Redis::OPT_PREFIX, $options['prefix'] . ':');
        }

        $this->redis = $redis;
    }

    /**
     *  Decides whether to return a Redis Instance or RedisArray Instance depending on the number of servers passed to it.
     *
     * @param array $servers The `concrete.cache.levels.{level}.drivers.redis.options` config item
     *
     * @return \Redis | \RedisArray
     */
    private function getRedisInstance(array $servers)
    {
        if (count($servers) == 1) {
            // If we only have one server in our array then we just reconnect to it
            $server = $servers[0];
            $redis = new Redis();

            if (isset($server['socket']) && $server['socket']) {
                $redis->connect($server['socket']);
            } else {
                $host = array_get($server, 'host', '');
                $port = array_get($server, 'port', 6379);
                $ttl = array_get($server, 'ttl', 0.5);
                // Check for both server/host - fallback due to cache using server
                $host = !empty($host) ? $host : array_get($server, 'server', '127.0.0.1');

                $redis->connect($host, $port, $ttl);
            }

            // Authorisation is handled by just a password
            if (isset($server['password'])) {
                $redis->auth($server['password']);
            }
        } else {
            $serverArray = [];
            $ttl = 0.5;
            $password = null;
            foreach ($this->getRedisServers($servers) as $server) {
                $serverString = $server['server'];
                if (isset($server['port'])) {
                    $serverString .= ':' . $server['port'];
                }
                // We can only use one ttl for connection timeout so use the last set ttl
                // isset allows for 0 - unlimited
                if (isset($server['ttl'])) {
                    $ttl = $server['ttl'];
                }
                if (isset($server['password'])) {
                    $password = $server['password'];
                }
                $serverArray[] = $serverString;
            }
            $options = ['connect_timeout' => $ttl];
            if ($password !== null) {
                $options['auth'] = $password;
            }
            $redis = new RedisArray($serverArray, $options);
        }

        return $redis;
    }

    /**
     * Generator for Redis Array.
     *
     * @param array $servers The `concrete.cache.{level}.redis.options.servers` config item
     *
     * @return \Generator| string[] [ $server, $port, $ttl ]
     */
    private function getRedisServers(array $servers)
    {
        if (!empty($servers)) {
            foreach ($servers as $server) {

                if (isset($server['socket'])) {
                    $server = [
                        'server' => array_get($server, 'socket', ''),
                        'ttl' => array_get($server, 'ttl', null),
                        'password' => array_get($server, 'password', null)
                    ];
                } else {
                    $host = array_get($server, 'host', '');
                    // Check for both server/host - fallback due to cache using server
                    $host = !empty($host) ?: array_get($server, 'server', '127.0.0.1');
                    $server = [
                        'server' => $host,
                        'port' => array_get($server, 'port', 11211),
                        'ttl' => array_get($server, 'ttl', null),
                        'password' => array_get($server, 'password', null)
                    ];
                }


                yield $server;
            }
        } else {
            yield ['server' => '127.0.0.1', 'port' => '6379', 'ttl' => 0.5];
        }
    }

    /**
     * Properly close the connection.
     */
    public function __destruct()
    {
        if ($this->redis instanceof \Redis) {
            try {
                $this->redis->close();
            } catch (\RedisException $e) {
                /*
                 * \Redis::close will throw a \RedisException("Redis server went away") exception if
                 * we haven't previously been able to connect to Redis or the connection has severed.
                 */
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getData($key)
    {
        return unserialize($this->redis->get($this->makeKeyString($key)));
    }

    /**
     * {@inheritdoc}
     */
    public function storeData($key, $data, $expiration)
    {
        $store = serialize(['data' => $data, 'expiration' => $expiration]);
        if (is_null($expiration)) {
            return $this->redis->set($this->makeKeyString($key), $store);
        } else {
            $ttl = $expiration - time();

            // Prevent us from even passing a negative ttl'd item to redis,
            // since it will just round up to zero and cache forever.
            if ($ttl < 1) {
                return true;
            }

            return $this->redis->setex($this->makeKeyString($key), $ttl, $store);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function clear($key = null)
    {
        if (is_null($key)) {
            // If we have a prefix delete only the prefix keys
            if (!empty($this->prefix)) {
                // This attaches the prefix to the keys
                $keys = $this->redis->getKeys('*');
                // Remove the prefix
                $this->redis->setOption(\Redis::OPT_PREFIX, null);
                // Delete all keys
                $this->redis->del($keys);
                // Reset the prefix
                $this->redis->setOption(\Redis::OPT_PREFIX, $this->prefix . ':');
            } else {
                $this->redis->flushDB();
            }

            return true;
        }

        $keyString = $this->makeKeyString($key, true);
        $keyReal = $this->makeKeyString($key);
        $this->redis->incr($keyString); // increment index for children items
        $this->redis->delete($keyReal); // remove direct item.
        $this->keyCache = [];

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function purge()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public static function isAvailable()
    {
        return class_exists('Redis', false);
    }

    /**
     * Turns a key array into a key string. This includes running the indexing functions used to manage the Redis
     * hierarchical storage.
     *
     * When requested the actual path, rather than a normalized value, is returned.
     *
     * @param  array  $key
     * @param  bool   $path
     *
     * @return string
     */
    protected function makeKeyString($key, $path = false)
    {
        $key = \Stash\Utilities::normalizeKeys($key);

        $keyString = 'cache:::';
        $pathKey = ':pathdb::';
        foreach ($key as $name) {
            //a. cache:::name
            //b. cache:::name0:::sub
            $keyString .= $name;

            //a. :pathdb::cache:::name
            //b. :pathdb::cache:::name0:::sub
            $pathKey = ':pathdb::' . $keyString;
            $pathKey = md5($pathKey);

            if (isset($this->keyCache[$pathKey])) {
                $index = $this->keyCache[$pathKey];
            } else {
                $index = $this->redis->get($pathKey);
                $this->keyCache[$pathKey] = $index;
            }

            //a. cache:::name0:::
            //b. cache:::name0:::sub1:::
            $keyString .= '_' . $index . ':::';
        }

        return $path ? $pathKey : md5($keyString);
    }

    /**
     * {@inheritdoc}
     */
    public function isPersistent()
    {
        return true;
    }
}
